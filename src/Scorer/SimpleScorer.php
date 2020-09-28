<?php

namespace Secry\FuzzyMatch\Scorer;

/**
 * 简单计分器
 *
 * 仅当目标字符串有查询字符串所有字符的顺序出现(不要求连续)即为匹配，否则为不匹配；没有具体分数，无法表达匹配程度
 */
class SimpleScorer extends BaseScorer
{
    const SCORE_SUCCESS = 1;
    const SCORE_FAILED  = 0;

    protected function scoreString(string $target, string $query): float
    {
        if (empty($query)) {
            return self::SCORE_SUCCESS;
        }

        $target = strtolower($target);
        $query  = strtolower($query);

        $targetChars = mb_str_split($target);
        $queryChars  = mb_str_split($query);

        while (null !== ($queryChar = array_shift($queryChars))) {
            while (null !== ($targetChar = array_shift($targetChars))) {
                if ($queryChar === $targetChar) {
                    // 匹配成功，跳到下一query字符
                    continue 2;
                }
            }
            // 如果target字符队列没有任何可以满足条件的字符时，直接失败
            return self::SCORE_FAILED;
        }

        return self::SCORE_SUCCESS;
    }
}
