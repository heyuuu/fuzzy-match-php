<?php

namespace Secry\FuzzyMatch\Test\Scorer;

use Secry\FuzzyMatch\Scorer\BonusScorer;
use Secry\FuzzyMatch\Scorer\SimpleScorer;
use Secry\FuzzyMatch\Scorer\WordSegmentationScorer;
use Secry\FuzzyMatch\Target\StringTarget;
use Secry\FuzzyMatch\Test\BaseTestCase;

class WordSegmentationScorerTest extends BaseTestCase
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
        $scorer = new WordSegmentationScorer(new SimpleScorer());

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
            // 分词匹配
            ['abc', ' abc ', 1],
            ['abc', ' abc ac', 1],
            ['abc', ' ab  ac', 1],
            ['abc', ' ab  ca', 0],
            ['abc', ' bc  ab ca', 0],
        ];
    }

    /**
     * 测试透传
     *
     * @dataProvider provideCompareInner
     *
     * @param string $targetString1
     * @param string $targetString2
     * @param string $query
     */
    public function testCompareInner(string $targetString1, string $targetString2, string $query)
    {
        $scorer  = new WordSegmentationScorer(new BonusScorer());
        $target1 = new StringTarget($targetString1);
        $target2 = new StringTarget($targetString2);

        $score1 = $scorer->score($target1, $query);
        $score2 = $scorer->score($target2, $query);

        $this->assertGreaterThan($score2, $score1);
    }

    public function provideCompareInner()
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

    /**
     * 测试分词叠加
     *
     * @dataProvider provideCompareQuery
     *
     * @param string $targetString
     * @param string $query1
     * @param string $query2
     */
    public function testCompareQuery(string $targetString, string $query1, string $query2)
    {
        $scorer = new WordSegmentationScorer(new BonusScorer());
        $target = new StringTarget($targetString);

        $score1 = $scorer->score($target, $query1);
        $score2 = $scorer->score($target, $query2);

        $this->assertGreaterThan($score2, $score1);
    }

    public function provideCompareQuery()
    {
        return [
            // 单个分数高,整体分数越高
            ['abc', 'ab ac', 'ac ac'],
            // 两个分数高，整体分数高
            ['abc', 'ab ab', 'ac ac'],
        ];
    }
}
