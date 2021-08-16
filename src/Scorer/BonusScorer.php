<?php

namespace Secry\FuzzyMatch\Scorer;

/**
 * Bonus计分器
 *
 * 根据不同匹配方式计算不同的得分(Bonus)，总计得分的计分器. 可以支持首字母、连续匹配等匹配方法得分更高的需求
 */
class BonusScorer extends BaseScorer
{
    // 计分情况
    const BONUS_BASE               = 'base';             // 匹配上的基础分
    const BONUS_SEQUENTIAL         = 'sequential';       // 连续匹配
    const BONUS_SEPARATOR          = 'separator';        // 单词首字符匹配
    const BONUS_CAMEL              = 'camel';            // 驼峰大写字符的加分
    const BONUS_FIRST_LETTER       = 'first_letter';     // 首个字符匹配
    const PENALTY_UNMATCHED_LETTER = 'unmatched_letter'; // 每个未匹配字符的惩罚，单位 分/字符
    const PENALTY_LEADING_LETTER   = 'leading_letter';   // 距离首个字符距离的惩罚分，单位 分/字符

    // 默认配置
    protected static $defaultConfig = [
        // 各计分情况分数
        self::BONUS_BASE               => 10000,
        self::BONUS_SEQUENTIAL         => 15,
        self::BONUS_SEPARATOR          => 30,
        self::BONUS_CAMEL              => 30,
        self::BONUS_FIRST_LETTER       => 15,
        self::PENALTY_LEADING_LETTER   => -5,
        self::PENALTY_UNMATCHED_LETTER => -1,
        // 其他配置
        'max_leading_letter'           => -15, // 距离首个字符距离的总惩罚分上限
        'max_match'                    => 255, // 最长匹配字符数，太长影响性能
        'max_recursive_limit'          => 10,  // 最大递归深度，避免爆栈
    ];

    /**
     * @var array
     */
    protected $config;

    /** @var string[] */
    private $targetChars;
    /** @var string[] */
    private $queryChars;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(static::$defaultConfig, $config);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    protected function scoreString(string $target, string $query): float
    {
        if (empty($query)) {
            return $this->config[self::BONUS_BASE];
        }

        $this->targetChars = mb_str_split(substr($target, 0, $this->config['max_match']));
        $this->queryChars  = mb_str_split($query);

        list($bestMatches, $bestScore) = $this->matchRecursive();

        return $bestScore;
    }

    protected function matchRecursive(int $targetIndex = 0, int $queryIndex = 0, array $matches = [], int $recursiveCount = 0)
    {
        // 终止条件处理
        if ($queryIndex === count($this->queryChars)) {
            // 全部query匹配完，范围当前匹配
            return [$matches, $this->calcScore($matches)];
        } elseif ($targetIndex >= count($this->targetChars)) {
            // 全部target使用完，返回未匹配
            return [[], 0];
        } elseif ($recursiveCount >= $this->config['max_recursive_limit']) {
            // 超过递归层级，返回未匹配
            return [[], 0];
        }

        $bestScore   = 0;
        $bestMatches = [];
        while ($targetIndex < count($this->targetChars) && $queryIndex < count($this->queryChars)) {
            $targetChar = strtolower($this->targetChars[$targetIndex]);
            $queryChar  = strtolower($this->queryChars[$queryIndex]);
            if ($targetChar === $queryChar) {
                // 获取当前未匹配时的最优结果
                [$aMatches, $aScore] = $this->matchRecursive($targetIndex + 1, $queryIndex, $matches, $recursiveCount + 1);
                if ($aScore > $bestScore) {
                    $bestScore   = $aScore;
                    $bestMatches = $aMatches;
                }

                // 当前匹配循环的步进
                $matches[] = $targetIndex;
                ++$queryIndex;
            }

            ++$targetIndex;
        }

        // 如果完全匹配
        if ($queryIndex === count($this->queryChars)) {
            $score = $this->calcScore($matches);
            if ($score > $bestScore) {
                $bestScore   = $score;
                $bestMatches = $matches;
            }
        }

        return [$bestMatches, $bestScore];
    }

    protected function calcScore(array $matches): float
    {
        // 初始化得分项
        $bonusItems = [
            self::BONUS_CAMEL              => 0,
            self::BONUS_SEQUENTIAL         => 0,
            self::BONUS_SEPARATOR          => 0,
            self::BONUS_CAMEL              => 0,
            self::BONUS_FIRST_LETTER       => 0,
            self::PENALTY_LEADING_LETTER   => 0,
            self::PENALTY_UNMATCHED_LETTER => 0,
        ];

        // 基础得分
        $bonusItems[self::BONUS_BASE] = 1;

        // 首个匹配距离的惩罚分
        $bonusItems[self::PENALTY_LEADING_LETTER] = $matches[0];

        // 每个未匹配字符的惩罚分
        $bonusItems[self::PENALTY_UNMATCHED_LETTER] = count($this->targetChars) - count($matches);

        // 逐匹配判断分数
        foreach ($matches as $queryIndex => $targetIndex) {
            // 连续匹配加分
            if ($queryIndex > 0 && $targetIndex === $matches[$queryIndex - 1] + 1) {
                $bonusItems[self::BONUS_SEQUENTIAL] += 1;
            }

            if ($targetIndex === 0) {
                // 首字符匹配的加分
                $bonusItems[self::BONUS_FIRST_LETTER] = 1;
            } else {
                // 驼峰大写字母匹配的加分
                if ($this->isUpperChar($this->targetChars[$targetIndex]) && $this->isLowerChar($this->targetChars[$targetIndex - 1])) {
                    $bonusItems[self::BONUS_CAMEL] += 1;
                }

                // 分隔符后首字符匹配的加分
                if ($this->isSeparatorChar($this->targetChars[$targetIndex - 1])) {
                    $bonusItems[self::BONUS_SEPARATOR] += 1;
                }
            }
        }

        // 计算分数
        $bonus = 0;
        foreach ($bonusItems as $bonusType => $bonusCount) {
            $bonus += $this->config[$bonusType] * $bonusCount;
        }

        return $bonus;
    }

    private function isLowerChar(string $char)
    {
        return $char === strtolower($char);
    }

    private function isUpperChar(string $char)
    {
        return $char === strtoupper($char);
    }

    private function isSeparatorChar(string $char)
    {
        return !(($char >= 'A' && $char <= 'Z') || ($char >= 'a' && $char <= 'z'));
    }
}
