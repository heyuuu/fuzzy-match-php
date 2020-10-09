<?php

namespace Secry\FuzzyMatch\Target;

use Secry\FuzzyMatch\MatchString;

class StringTarget extends StandardTarget
{
    public function __construct($string)
    {
        parent::__construct($string, [new MatchString($string)]);
    }
}
