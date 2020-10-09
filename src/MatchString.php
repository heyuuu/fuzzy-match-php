<?php

namespace Secry\FuzzyMatch;

class MatchString
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var float
     */
    private $weight;

    public function __construct(string $string, float $weight = 1)
    {
        $this->string = $string;
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
}
