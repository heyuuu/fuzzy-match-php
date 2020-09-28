<?php

namespace Secry\FuzzyMatch;

use Secry\FuzzyMatch\Contract\Scorer;
use Secry\FuzzyMatch\Contract\Target;
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

    public function __construct(array $targets, Scorer $scorer = null)
    {
        Assert::allIsInstanceOf($targets, Target::class);

        $this->targets = $targets;
        $this->scorer  = $scorer ?: $this->initDefaultScorer();
    }

    private function initDefaultScorer(): Scorer
    {
        // todo 新建默认计分器
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
     * @param string $query
     *
     * @return Target[]
     */
    public function match(string $query): array
    {
        // todo 匹配的核心逻辑
    }
}
