<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\Exception\RuneScapeException;

class HighScoreActivity extends HighScoreEntry
{
    /** @var Activity */
    protected $activity;

    /** @var int */
    protected $score;

    public function __construct(Activity $activity, int $rank, int $score)
    {
        parent::__construct($rank);

        $this->activity = $activity;

        if ($score < 0) {
            $score = 0;
        }

        $this->score = $score;
    }

    /**
     * Returns the name of the stat for display purposes.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->activity->getName();
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
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param HighScoreActivity $otherActivity
     * @return HighScoreComparisonActivity
     * @throws RuneScapeException
     */
    public function compareTo(HighScoreActivity $otherActivity): HighScoreComparisonActivity
    {
        return new HighScoreComparisonActivity($this, $otherActivity);
    }
}
