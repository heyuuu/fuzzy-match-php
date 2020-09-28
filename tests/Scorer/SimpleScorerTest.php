<?php

namespace Secry\FuzzyMatch\Test\Scorer;

use Secry\FuzzyMatch\Scorer\SimpleScorer;
use Secry\FuzzyMatch\Target\StringTarget;
use Secry\FuzzyMatch\Test\BaseTestCase;

class SimpleScorerTest extends BaseTestCase
{
    /**
     * @dataProvider provideBase
     *
     * @param string $targetString
     * @param string $query
     * @param float  $exceptedScore
     */
    public function testBase(string $targetString, string $query, float $exceptedScore)
    {
        $scorer = new SimpleScorer();

        $target = new StringTarget($targetString);
        $score  = $scorer->score($target, $query);

        $this->assertEquals($exceptedScore, $score);
    }

    public function provideBase()
    {
        return [
            // 基础匹配
            ['abc', '', 1],
            ['abc', 'abc', 1],
            ['abc', 'ac', 1],
            ['abdbc', 'adc', 1],
            ['abc', 'ac', 1],
            ['abcd', 'acb', 0],
            ['abc', 'abbc', 0],
        ];
    }
}
