<?php

namespace Villermen\RuneScape;

class HighscoreActivity extends HighscoreEntry
{
    /** @var Activity */
    private $activity;

    /** @var int */
    private $score;

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
     * @return HighscoreActivity
     */
    private function setScore(int $score)
    {
        if ($score < 0) {
            $score = 0;
        }

        $this->score = $score;

        return $this;
    }
}
