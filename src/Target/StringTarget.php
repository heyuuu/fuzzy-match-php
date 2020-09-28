<?php

namespace Secry\FuzzyMatch\Target;

class StringTarget extends StandardTarget
{
    public function __construct($string)
    {
        parent::__construct($string, $string, $string);
    }
}
