<?php

namespace Villermen\RuneScape\HighScore;

/**
 * A skill entry of a player's high score.
 *
 * @template-covariant TSkill of SkillInterface
 */
class HighScoreSkill
{
    /**
     * @param TSkill $skill
     */
    public function __construct(
        public readonly SkillInterface $skill,
        public readonly ?int $rank,
        public readonly ?int $level,
        public readonly ?int $xp,
    ) {
    }

    public function getLevelOrMinimum(): int
    {
        return $this->level ?? $this->skill->getMinimumLevel();
    }

    public function getVirtualLevel(): ?int
    {
        if ($this->isTotal()) {
            return $this->level;
        }

        if ($this->getXp() === null) {
            return null;
        }

        return $this->skill->getVirtualLevel($this->getXp());
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function getName(): string
    {
        return $this->skill->getName();
    }

    public function isTotal(): bool
    {
        return in_array($this->skill, [Rs3Skill::TOTAL, OsrsSkill::TOTAL]);
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

        $nextXp = $this->skill->getXp($virtualLevel + 1);
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

        $currentLevelXp = $this->skill->getXp($virtualLevel);
        $nextLevelXp = $this->skill->getXp($virtualLevel + 1);
        if ($currentLevelXp === null || $nextLevelXp === null) {
            return null;
        }

        $totalXpInLevel = $nextLevelXp - $currentLevelXp;
        $xpInLevel = $this->getXp() - $currentLevelXp;

        return $xpInLevel / $totalXpInLevel;
    }
}
