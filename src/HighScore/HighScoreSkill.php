<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\RuneScapeException;
use Villermen\RuneScape\Skill;

/**
 * A skill entry of a player's high score.
 */
class HighScoreSkill extends HighScoreEntry
{
    /** @var Skill */
    protected $skill;

    /** @var int */
    protected $level;

    /** @var int */
    protected $xp;

    public function __construct(Skill $skill, int $rank, int $level, int $xp)
    {
        parent::__construct($rank);

        $this->skill = $skill;

        if ($level < 1) {
            $level = $skill->getMinimumLevel();
        }

        $this->level = $level;

        if ($xp < 0) {
            $xp = 0;
        }

        $this->xp = $xp;
    }

    /**
     * @return int|false
     */
    public function getXpToNextLevel()
    {
        $level = $this->getLevel(true);

        $nextXp = $this->getSkill()->getXp($level + 1);

        if (!$nextXp) {
            return false;
        }

        return $nextXp - $this->getXp();
    }

    /**
     * Returns how far to the next level this skill is from 0-1.
     *
     * @return float|false
     */
    public function getProgressToNextLevel()
    {
        $level = $this->getLevel(true);
        $currentLevelXp = $this->getSkill()->getXp($level);
        $nextLevelXp = $this->getSkill()->getXp($level + 1);

        if (!$nextLevelXp) {
            return false;
        }

        $totalXpInLevel = $nextLevelXp - $currentLevelXp;
        $xpInLevel = $this->getXp() - $currentLevelXp;

        return $xpInLevel / $totalXpInLevel;
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }

    /**
     * @param bool $uncapped
     * @return int
     */
    public function getLevel(bool $uncapped = false): int
    {
        // Total level cannot be uncapped as it does not map to an XP table
        if ($uncapped && $this->skill->getId() !== Skill::SKILL_TOTAL) {
            return $this->getSkill()->getLevel($this->getXp(), true);
        }

        return $this->level;
    }

    /**
     * @return int
     */
    public function getXp(): int
    {
        return $this->xp;
    }

    /** @inheritdoc */
    public function getName(): string
    {
        return $this->getSkill()->getName();
    }

    /**
     * @param HighScoreSkill $otherSkill
     * @return HighScoreComparisonSkill
     */
    public function compareTo(HighScoreSkill $otherSkill): HighScoreComparisonSkill
    {
        return new HighScoreComparisonSkill($this, $otherSkill);
    }
}
