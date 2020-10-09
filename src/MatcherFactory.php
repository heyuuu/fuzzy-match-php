<?php

namespace Secry\FuzzyMatch;

use Secry\FuzzyMatch\Target\StandardTarget;
use Webmozart\Assert\Assert;

class MatcherFactory
{
    public function create(array $targets)
    {
        return new Matcher($targets);
    }

    public function createByStrings(array $strings)
    {
        $targets = [];
        foreach ($strings as $string) {
            $targets[] = $this->createTarget($string, $string, $string);
        }

        return $this->create($targets);
    }

    public function createByStringMap(array $map)
    {
        Assert::allStringNotEmpty(array_keys($map));
        $targets = [];
        foreach ($map as $stringKey => $result) {
            $targets[] = $this->createTarget($stringKey, $result, $stringKey);
        }

        return $this->create($targets);
    }

    public function createTarget($key, $result, $matchString)
    {
        return new StandardTarget($key, $result);
    }
}
