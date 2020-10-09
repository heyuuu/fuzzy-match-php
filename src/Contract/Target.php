<?php

namespace Secry\FuzzyMatch\Contract;

use Secry\FuzzyMatch\MatchString;

interface Target
{
    public function getResult();

    /**
     * @return MatchString[]
     */
    public function getMatchStrings(): array;
}
