<?php

namespace Secry\FuzzyMatch\Scorer;

use Secry\FuzzyMatch\Contract\Scorer;
use Secry\FuzzyMatch\Contract\Target;
use Tightenco\Collect\Support\Collection;

/**
 * 带分词的计分器
 */
class WordSegmentationScorer implements Scorer
{
    const SCORE_SUCCESS = 1;
    const SCORE_FAILED  = 0;

    /**
     * @var Scorer
     */
    private $innerScorer;

    public function __construct(Scorer $innerScore)
    {
        $this->innerScorer = $innerScore;
    }

    /**
     * @return Scorer
     */
    public function getInnerScorer(): Scorer
    {
        return $this->innerScorer;
    }

    public function score(Target $target, string $query): float
    {
        if (empty($query)) {
            return self::SCORE_SUCCESS;
        }

        $segments = Collection::make(explode(' ', $query))->map('trim')->filter();

        $score = 1;
        foreach ($segments as $segment) {
            $score *= $segmentScore = $this->innerScorer->score($target, $segment);
            if ($score === 0) {
                return $score;
            }
        }

        return $score;
    }
}
