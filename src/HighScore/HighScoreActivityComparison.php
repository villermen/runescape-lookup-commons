<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\RuneScapeException;

class HighScoreActivityComparison extends HighScoreEntryComparison
{
    /** @var int */
    protected $scoreDifference = false;

    public function __construct(HighScoreActivity $activity1, HighScoreActivity $activity2)
    {
        if ($activity1->getActivity() !== $activity2->getActivity()) {
            throw new RuneScapeException("Can't compare two different highscore activities.");
        }

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
