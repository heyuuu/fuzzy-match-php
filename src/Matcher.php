<?php

namespace Secry\FuzzyMatch;

use Secry\FuzzyMatch\Contract\Scorer;
use Secry\FuzzyMatch\Contract\Target;
use Secry\FuzzyMatch\Scorer\BonusScorer;
use Secry\FuzzyMatch\Scorer\WordSegmentationScorer;
use Secry\FuzzyMatch\Target\StringTarget;
use Tightenco\Collect\Support\Collection;
use Webmozart\Assert\Assert;

class Matcher
{
    /**
     * @var array
     */
    private $targets;

    /**
     * @var Scorer
     */
    private $scorer;

    /**
     * 快捷创建一个匹配器
     *
     * @param Target[] $targets
     *
     * @return Matcher
     */
    public static function create(array $targets)
    {
        return new static($targets);
    }

    /**
     * 快捷创建一个匹配字符串的匹配器
     *
     * @param array $strings
     *
     * @return Matcher
     */
    public static function createByStrings(array $strings)
    {
        Assert::allStringNotEmpty($strings);
        $targets = array_map(function (string $string) {
            return new StringTarget($string);
        }, $strings);

        return new Matcher($targets);
    }

    public function __construct(array $targets, Scorer $scorer = null)
    {
        Assert::allIsInstanceOf($targets, Target::class);

        $this->targets = $targets;
        $this->scorer  = $scorer ?: $this->initDefaultScorer();
    }

    /**
     * 默认计分器
     *
     * @return Scorer
     */
    protected function initDefaultScorer(): Scorer
    {
        return new WordSegmentationScorer(new BonusScorer());
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @return Scorer
     */
    public function getScorer(): Scorer
    {
        return $this->scorer;
    }

    /**
     * 匹配目标Target并返回result
     *
     * @param string $query
     *
     * @return mixed[]
     */
    public function match(string $query): array
    {
        $targets = $this->matchTargets($query);

        return array_map(function (Target $target) {
            return $target->getResult();
        }, $targets);
    }

    /**
     * 匹配目标Target
     *
     * @param string $query
     *
     * @return Target[]
     */
    public function matchTargets(string $query): array
    {
        if (empty($query)) {
            return $this->targets;
        } else {
            return $this->factualMatch($query, $this->targets);
        }
    }

    /**
     * 实际匹配目标的操作
     *
     * @param string   $query
     * @param Target[] $targets
     *
     * @return Target[]
     */
    protected function factualMatch(string $query, array $targets)
    {
        return Collection::make($targets)
            ->map(function (Target $target) use ($query) {
                $score = $this->scorer->score($target, $query);

                return $score > 0 ? ['score' => $score, 'target' => $target] : null;
            })
            ->filter()
            ->sortByDesc('score')
            ->pluck('target')
            ->all();
    }
}
