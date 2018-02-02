<?php

namespace Villermen\RuneScape\HighScore;

class ActivityHighScore
{
    /** @var HighScoreActivity[] */
    protected $activities;

    /**
     * Creates a HighScore object from raw high score data.
     *
     * @param HighScoreActivity[] $activities
     */
    public function __construct(array $activities)
    {
        $this->activities = [];
        foreach($activities as $activity) {
            $this->activities[$activity->getActivity()->getId()] = $activity;
        }

        ksort($this->activities);
    }

    /**
     * @return HighScoreActivity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param $id
     * @return HighScoreActivity|null
     */
    public function getActivity($id)
    {
        if (!isset($this->activities[$id])) {
            return null;
        }

        return $this->activities[$id];
    }

    /**
     * Creates a HighScoreComparison between this high score and the given high score.
     *
     * @param ActivityHighScore $otherHighScore
     * @return ActivityHighScoreComparison
     */
    public function compareTo(ActivityHighScore $otherHighScore): ActivityHighScoreComparison
    {
        return new ActivityHighScoreComparison($this, $otherHighScore);
    }
}
