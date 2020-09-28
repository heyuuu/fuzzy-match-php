<?php

namespace Secry\FuzzyMatch\Contracts;

interface Scorer
{
    public function score(Target $target, string $query): float;
}
