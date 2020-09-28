<?php

namespace Secry\FuzzyMatch\Contract;

interface Scorer
{
    public function score(Target $target, string $query): float;
}
