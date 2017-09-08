<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Activity;

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
        $this->setScore($score);
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
     * @param int $score
     *
     * @return HighScoreActivity
     */
    private function setScore(int $score)
    {
        if ($score < 0) {
            $score = 0;
        }

        $this->score = $score;

        return $this;
    }

    /**
     * @param HighScoreEntry $entry
     * @return HighScoreEntryComparison|HighScoreActivityComparison
     */
    public function compareTo(HighScoreEntry $entry): HighScoreEntryComparison
    {
        if ($entry instanceof HighScoreActivity) {
            return new HighScoreActivityComparison($this, $entry);
        }

        return parent::compareTo($entry);
    }
}
