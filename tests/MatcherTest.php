<?php

namespace Secry\FuzzyMatch\Test;

use Secry\FuzzyMatch\Contract\Target;
use Secry\FuzzyMatch\Matcher;
use Secry\FuzzyMatch\MatchString;
use Secry\FuzzyMatch\Target\StandardTarget;
use Secry\FuzzyMatch\Target\StringTarget;

class MatcherTest extends BaseTestCase
{
    public function testBase()
    {
        $targets = [
            new StringTarget('abc'),
            new StandardTarget('some result', [new MatchString('ab test')]),
        ];
        $matcher = new Matcher($targets);

        $result = $matcher->match('ab');
        $this->assertIsArray($result);
        foreach ($result as $itemResult) {
            $this->assertInstanceOf(Target::class, $itemResult);
        }
    }
}
