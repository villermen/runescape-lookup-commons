<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;

class ActivityHighScoreComparison
{
    /** @var HighScoreActivityComparison[] */
    protected $activities = [];

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param ActivityHighScore $highScore1
     * @param ActivityHighScore $highScore2
     */
    public function __construct(ActivityHighScore $highScore1, ActivityHighScore $highScore2)
    {
        $activityIds = array_unique(array_merge(
            array_keys($highScore1->getActivities()),
            array_keys($highScore2->getActivities())
        ));

        foreach($activityIds as $activityId) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->activities[$activityId] = new HighScoreActivityComparison(
                $highScore1->getActivity($activityId),
                $highScore2->getActivity($activityId)
            );
        }
    }

    /**
     * @return HighScoreActivityComparison[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param int $id
     * @return HighScoreActivityComparison
     * @throws RuneScapeException
     */
    public function getActivity(int $id): HighScoreActivityComparison
    {
        if (!isset($this->activities[$id])) {
            throw new RuneScapeException("Neither of the high scores contains the requested activity.");
        }

        return $this->activities[$id];
    }
}
