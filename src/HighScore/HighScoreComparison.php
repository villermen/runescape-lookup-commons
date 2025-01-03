<?php

namespace Villermen\RuneScape\HighScore;

class HighScoreComparison
{
    public function __construct(
        protected readonly HighScore $highScore1,
        protected readonly HighScore $highScore2,
    ) {
        if ($this->highScore1 instanceof OsrsHighScore !== $this->highScore2 instanceof OsrsHighScore) {
            throw new \InvalidArgumentException(
                'Highscore comparison can only be created between highscores of the same game version.'
            );
        }
    }

    public function getRankDifference(SkillInterface|ActivityInterface $entry): ?int
    {
        $entry1 = $entry instanceof ActivityInterface ? $this->highScore1->getActivity($entry) : $this->highScore1->getSkill($entry);
        $entry2 = $entry instanceof ActivityInterface ? $this->highScore2->getActivity($entry) : $this->highScore2->getSkill($entry);
        if ($entry1->getRank() === null || $entry2->getRank() === null) {
            return null;
        }

        return $entry2->getRank() - $entry1->getRank();
    }

    public function getXpDifference(SkillInterface $skill): ?int
    {
        $skill1 = $this->highScore1->getSkill($skill);
        $skill2 = $this->highScore2->getSkill($skill);
        if ($skill1->getXp() === null || $skill2->getXp() === null) {
            return null;
        }

        return $skill1->getXp() - $skill2->getXp();
    }

    public function getLevelDifference(SkillInterface $skill): ?int
    {
        $skill1 = $this->highScore1->getSkill($skill);
        $skill2 = $this->highScore2->getSkill($skill);
        if ($skill1->getLevel() === null || $skill2->getLevel() === null) {
            return null;
        }

        return $skill1->getLevel() - $skill2->getLevel();
    }

    public function getVirtualLevelDifference(SkillInterface $skill): ?int
    {
        $skill1 = $this->highScore1->getSkill($skill);
        $skill2 = $this->highScore2->getSkill($skill);
        if ($skill1->getVirtualLevel() === null || $skill2->getVirtualLevel() === null) {
            return null;
        }

        return $skill1->getVirtualLevel() - $skill2->getVirtualLevel();
    }

    public function getScoreDifference(ActivityInterface $activity): ?int
    {
        $activity1 = $this->highScore1->getActivity($activity);
        $activity2 = $this->highScore2->getActivity($activity);
        if ($activity1->getScore() === null || $activity2->getScore() === null) {
            return null;
        }

        return $activity1->getScore() - $activity2->getScore();
    }
}
