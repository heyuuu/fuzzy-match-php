<?php

namespace Secry\FuzzyMatch\Test\Scorer;

use Secry\FuzzyMatch\Scorer\BonusScorer;
use Secry\FuzzyMatch\Target\StringTarget;
use Secry\FuzzyMatch\Test\BaseTestCase;

class BonusScorerTest extends BaseTestCase
{
    /**
     * @dataProvider provideBase
     *
     * @param string $targetString
     * @param string $query
     * @param bool   $matched
     */
    public function testBase(string $targetString, string $query, bool $matched)
    {
        $scorer = new BonusScorer();
        $target = new StringTarget($targetString);
        $score  = $scorer->score($target, $query);

        $this->assertEquals($matched, $score > 0);
    }

    public function provideBase()
    {
        return [
            // 基础匹配
            ['abc', '', true],
            ['abc', 'abc', true],
            ['abc', 'ac', true],
            ['abdbc', 'adc', true],
            ['abc', 'ac', true],
            ['abcd', 'acb', false],
            ['abc', 'abbc', false],
        ];
    }

    /**
     * @dataProvider provideCompare
     *
     * @param string $targetString1
     * @param string $targetString2
     * @param string $query
     */
    public function testCompare(string $targetString1, string $targetString2, string $query)
    {
        $scorer  = new BonusScorer();
        $target1 = new StringTarget($targetString1);
        $target2 = new StringTarget($targetString2);

        $score1 = $scorer->score($target1, $query);
        $score2 = $scorer->score($target2, $query);

        $this->assertGreaterThan($score2, $score1);
    }

    public function provideCompare()
    {
        return [
            // 基础分
            ['abc', 'ab', 'ac'],
            // 连续匹配
            ['abc', 'acb', 'ab'],
            // 首个字符匹配
            ['abc', 'cab', 'ab'],
            // 驼峰大写字符的加分
            ['aBc', 'abc', 'ab'],
            // 单词首字符匹配
            ['Aaa bbb', 'Aaaabbb', 'AB'],
            // 每个未匹配字符的惩罚
            ['abc', 'abcd', 'ab'],
            // 距离首个字符距离的惩罚分
            ['xabcx', 'xxabc', 'abc'],
        ];
    }
}
