<?php

namespace Secry\FuzzyMatcher\Targets;

use Secry\FuzzyMatch\Contracts\Target;

class StandardTarget implements Target
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $matchString;

    /**
     * @var mixed
     */
    private $result;

    /**
     * StandardTarget constructor.
     *
     * @param string $key
     * @param mixed  $result
     * @param string $matchString
     */
    public function __construct(string $key, $result, string $matchString)
    {
        $this->key         = $key;
        $this->matchString = $matchString;
        $this->result      = $result;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getMatchString()
    {
        return $this->matchString;
    }
}
