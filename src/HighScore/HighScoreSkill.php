<?php

namespace Villermen\RuneScape\HighScore;

/**
 * A skill entry of a player's high score.
 *
 * @template TSkill of SkillInterface
 */
class HighScoreSkill
{
    /**
     * @param TSkill $skill
     */
    public function __construct(
        protected readonly SkillInterface $skill,
        protected readonly ?int $rank,
        protected readonly ?int $level,
        protected readonly ?int $xp,
    ) {
    }

    /**
     * @return TSkill
     */
    public function getSkill(): SkillInterface
    {
        return $this->skill;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function getLevelOrMinimum(): int
    {
        return $this->getLevel() ?? $this->getSkill()->getMinimumLevel();
    }

    public function getVirtualLevel(): ?int
    {
        if ($this->isTotal()) {
            return $this->getLevel();
        }

        if ($this->getXp() === null) {
            return null;
        }

        return $this->getSkill()->getVirtualLevel($this->getXp());
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function getName(): string
    {
        return $this->getSkill()->getName();
    }

    public function isTotal(): bool
    {
        return in_array($this->getSkill(), [Rs3Skill::TOTAL, OsrsSkill::TOTAL]);
    }

    public function getXpToNextLevel(): ?int
    {
        if ($this->isTotal() || $this->getXp() === null) {
            return null;
        }

        $virtualLevel = $this->getVirtualLevel();
        if ($virtualLevel === null) {
            return null;
        }

        $nextXp = $this->getSkill()->getXp($virtualLevel + 1);
        if (!$nextXp) {
            return null;
        }

        return $nextXp - $this->getXp();
    }

    /**
     * Returns how far to the next level this skill is from 0-1.
     */
    public function getProgressToNextLevel(): ?float
    {
        if ($this->isTotal() || $this->getXp() === null) {
            return null;
        }

        $virtualLevel = $this->getVirtualLevel();
        if ($virtualLevel === null) {
            return null;
        }

        $currentLevelXp = $this->getSkill()->getXp($virtualLevel);
        $nextLevelXp = $this->getSkill()->getXp($virtualLevel + 1);
        if ($currentLevelXp === null || $nextLevelXp === null) {
            return null;
        }

        $totalXpInLevel = $nextLevelXp - $currentLevelXp;
        $xpInLevel = $this->getXp() - $currentLevelXp;

        return $xpInLevel / $totalXpInLevel;
    }
}
