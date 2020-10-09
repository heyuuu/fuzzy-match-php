<?php

namespace Secry\FuzzyMatch\Scorer;

use Secry\FuzzyMatch\Contract\Scorer;
use Secry\FuzzyMatch\Contract\Target;
use Secry\FuzzyMatch\MatchString;
use Tightenco\Collect\Support\Collection;

abstract class BaseScorer implements Scorer
{
    public function score(Target $target, string $query): float
    {
        return Collection::make($target->getMatchStrings())
            ->map(function (MatchString $matchString) use ($query) {
                return $matchString->getWeight() * $this->scoreString($matchString->getString(), $query);
            })
            ->sum();
    }

    /**
     * 对当个字符串的匹配
     *
     * @param string $target
     * @param string $query
     *
     * @return float
     */
    abstract protected function scoreString(string $target, string $query): float;
}
