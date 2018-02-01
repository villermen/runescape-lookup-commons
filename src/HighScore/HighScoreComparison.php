<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;

/**
 * Represents a comparison between two high scores.
 * Contains comparisons for all skills and activities found in either of the high scores.
 */
class HighScoreComparison
{
    /** @var HighScoreComparisonSkill[] */
    private $skills = [];

    /** @var HighScoreComparisonActivity[] */
    private $activities = [];

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * HighScoreComparison constructor.
     * @param HighScore $highScore1
     * @param HighScore $highScore2
     */
    public function __construct(HighScore $highScore1, HighScore $highScore2)
    {
        $skillIds = array_unique(array_merge(
            array_keys($highScore1->getSkills()),
            array_keys($highScore2->getSkills())
        ));

        foreach($skillIds as $skillId) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->skills[$skillId] = new HighScoreComparisonSkill(
                $highScore1->getSkill($skillId),
                $highScore2->getSkill($skillId)
            );
        }

        $activityIds = array_unique(array_merge(
            array_keys($highScore1->getActivities()),
            array_keys($highScore2->getActivities())
        ));

        foreach($activityIds as $activityId) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->activities[$activityId] = new HighScoreComparisonActivity(
                $highScore1->getActivity($activityId),
                $highScore2->getActivity($activityId)
            );
        }
    }

    /**
     * @return HighScoreComparisonSkill[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param int $id
     * @return HighScoreComparisonSkill
     * @throws RuneScapeException
     */
    public function getSkill(int $id): HighScoreComparisonSkill
    {
        if (!isset($this->skills[$id])) {
            throw new RuneScapeException("Neither of the high scores contains the requested skill.");
        }

        return $this->skills[$id];
    }

    /**
     * @return HighScoreComparisonActivity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param int $id
     * @return HighScoreComparisonActivity
     * @throws RuneScapeException
     */
    public function getActivity(int $id): HighScoreComparisonActivity
    {
        if (!isset($this->activities[$id])) {
            throw new RuneScapeException("Neither of the high scores contains the requested activity.");
        }

        return $this->activities[$id];
    }
}
