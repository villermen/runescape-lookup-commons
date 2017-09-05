<?php

namespace Villermen\RuneScape\HighScore;

class HighScoreActivityComparison extends HighScoreEntryComparison
{
    /** @var int */
    private $scoreDifference = false;

    public function __construct(HighScoreActivity $activity1, HighScoreActivity $activity2)
    {
        parent::__construct($activity1, $activity2);

        $this->scoreDifference = $activity1->getScore() - $activity2->getScore();
    }

    /**
     * @return int
     */
    public function getScoreDifference(): int
    {
        return $this->scoreDifference;
    }
}
