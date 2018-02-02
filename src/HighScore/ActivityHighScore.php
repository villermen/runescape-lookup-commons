<?php

namespace Villermen\RuneScape\HighScore;

class ActivityHighScore extends HighScore
{
    /** @var HighScoreActivity[] */
    protected $activities = [];

    /**
     * Creates a HighScore object from raw high score data.
     *
     * @param HighScoreActivity[] $activities
     * @param bool $oldSchool
     */
    public function __construct(array $activities, bool $oldSchool)
    {
        parent::__construct($oldSchool);

        // Ensure that activities have their id as array key
        array_walk($activities, function(HighScoreActivity $activity, &$key) {
            $key = $activity->getActivity()->getId();
        });

        $this->activities = $activities;
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
