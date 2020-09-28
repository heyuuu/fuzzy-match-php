<?php

namespace Secry\FuzzyMatch\Contracts;

interface Target
{
    public function getKey();

    public function getResult();

    public function getMatchString();
}
