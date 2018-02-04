<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\Exception\RuneScapeException;

class HighScoreActivityComparison extends HighScoreEntryComparison
{
    /** @var Activity */
    protected $activity;

    /** @var int */
    protected $scoreDifference;

    /**
     * HighScoreComparisonActivity constructor.
     * @param HighScoreActivity|null $activity1
     * @param HighScoreActivity|null $activity2
     * @throws RuneScapeException
     */
    public function __construct($activity1, $activity2)
    {
        parent::__construct($activity1, $activity2);

        if (!$activity1) {
            $activity1 = new HighScoreActivity($activity2->getActivity(), -1, -1);
        }

        if (!$activity2) {
            $activity2 = new HighScoreActivity($activity1->getActivity(), -1, -1);
        }

        if ($activity1->getActivity() !== $activity2->getActivity()) {
            throw new RuneScapeException("Can't compare two different activities.");
        }

        $this->activity = $activity1->getActivity();
        $this->scoreDifference = $activity1->getScore() - $activity2->getScore();
    }

    /**
     * @return Activity
     */
    public function getActivity(): Activity
    {
        return $this->activity;
    }

    /**
     * @return int
     */
    public function getScoreDifference(): int
    {
        return $this->scoreDifference;
    }
}
