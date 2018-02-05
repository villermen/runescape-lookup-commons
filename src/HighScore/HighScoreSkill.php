<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;
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
        $this->level = max(1, $level);
        $this->xp = max(0, $xp);
    }

    /**
     * @return int|false
     */
    public function getXpToNextLevel()
    {
        if ($this->isTotal()) {
            return false;
        }

        $level = $this->getVirtualLevel();
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
        if ($this->isTotal()) {
            return false;
        }

        $level = $this->getVirtualLevel();
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
     * @return int
     */
    public function getLevel(): int
    {
        if ($this->isTotal()) {
            return $this->level;
        }

        return $this->getSkill()->getLevel($this->xp);
    }

    /**
     * @return int
     */
    public function getVirtualLevel(): int
    {
        if ($this->isTotal()) {
            return $this->level;
        }

        return $this->getSkill()->getVirtualLevel($this->xp);
    }

    /**
     * @return int
     */
    public function getXp(): int
    {
        // Match minimum XP required if skill has a higher minimum level
        if (!$this->xp && $this->getLevel() > 1) {
            return $this->getSkill()->getXp($this->getLevel());
        }

        return $this->xp;
    }

    /** @inheritdoc */
    public function getName(): string
    {
        return $this->getSkill()->getName();
    }

    /**
     * @return bool
     */
    public function isTotal(): bool
    {
        return $this->skill->getId() === Skill::SKILL_TOTAL;
    }

    /**
     * @param HighScoreSkill $otherSkill
     * @return HighScoreSkillComparison
     * @throws RuneScapeException
     */
    public function compareTo(HighScoreSkill $otherSkill): HighScoreSkillComparison
    {
        return new HighScoreSkillComparison($this, $otherSkill);
    }
}
