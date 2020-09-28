<?php

namespace Secry\FuzzyMatch\Scorer;

use Secry\FuzzyMatch\Contract\Scorer;
use Secry\FuzzyMatch\Contract\Target;

abstract class BaseScorer implements Scorer
{
    public function score(Target $target, string $query): float
    {
        // todo 此处保留以后做多匹配字符串的入口
        return $this->scoreString($target->getMatchString(), $query);
    }

    abstract protected function scoreString(string $target, string $query): float;
}
