<?php

namespace Secry\FuzzyMatch\Target;

use Secry\FuzzyMatch\Contract\Target;
use Secry\FuzzyMatch\MatchString;
use Webmozart\Assert\Assert;

class StandardTarget implements Target
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @var array|MatchString[]
     */
    private $matchStrings;

    /**
     * StandardTarget constructor.
     *
     * @param mixed         $result
     * @param MatchString[] $matchStrings
     */
    public function __construct($result, array $matchStrings)
    {
        Assert::allIsInstanceOf($matchStrings, MatchString::class);

        $this->result       = $result;
        $this->matchStrings = $matchStrings;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return MatchString[]
     */
    public function getMatchStrings(): array
    {
        return $this->matchStrings;
    }
}
