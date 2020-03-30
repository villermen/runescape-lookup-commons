<?php

namespace Villermen\RuneScape\HighScore;

class ActivityHighScore
{
    /** @var HighScoreActivity[] */
    protected $activities;

    /**
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

    public function getActivity(int $id): ?HighScoreActivity
    {
        return ($this->activities[$id] ?? null);
    }

    public function compareTo(ActivityHighScore $otherHighScore): ActivityHighScoreComparison
    {
        return new ActivityHighScoreComparison($this, $otherHighScore);
    }
}
