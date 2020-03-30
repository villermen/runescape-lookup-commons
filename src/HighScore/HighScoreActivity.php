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
     */
    public function getName(): string
    {
        return $this->activity->getName();
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param HighScoreActivity $otherActivity
     * @return HighScoreActivityComparison
     * @throws RuneScapeException
     */
    public function compareTo(HighScoreActivity $otherActivity): HighScoreActivityComparison
    {
        return new HighScoreActivityComparison($this, $otherActivity);
    }
}
