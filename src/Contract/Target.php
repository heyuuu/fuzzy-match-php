<?php

namespace Secry\FuzzyMatch\Contract;

interface Target
{
    public function getKey();

    public function getResult();

    public function getMatchString();
}
