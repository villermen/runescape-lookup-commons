<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Activity;

class ActivityHighScoreComparison
{
    /** @var HighScoreActivityComparison[] */
    protected $activities = [];

    public function __construct(ActivityHighScore $highScore1, ActivityHighScore $highScore2)
    {
        $activityIds = array_unique(array_merge(
            array_keys($highScore1->getActivities()),
            array_keys($highScore2->getActivities())
        ));

        foreach($activityIds as $activityId) {
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

    public function hasActivity(int $id): bool
    {
        return isset($this->activities[$id]);
    }

    /**
     * @param int $id One of the {@see Activity}::ACTIVITY_* constants.
     */
    public function getActivity(int $id): HighScoreActivityComparison
    {
        if (!$this->hasActivity($id)) {
            throw new \InvalidArgumentException("Neither of the high scores contains the requested activity.");
        }

        return $this->activities[$id];
    }
}
