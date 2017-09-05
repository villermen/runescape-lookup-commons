<?php

namespace Villermen\RuneScape\Highscore;

use Villermen\RuneScape\Skill;

/**
 * A skill entry of a player's highscore.
 */
class HighscoreSkill extends HighscoreEntry
{
    /** @var Skill */
    private $skill;

    /** @var int */
    private $level;

    /** @var int */
    private $xp;

    public function __construct(Skill $skill, int $rank, int $level, int $xp)
    {
        parent::__construct($rank);

        $this->skill = $skill;
        $this->setLevel($level);
        $this->setXp($xp);
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
        $currentLevelXp = $this->getSkill()->getXp($this->getLevel(true));
        $nextLevelXp = $this->getXpToNextLevel();

        if (!$nextLevelXp) {
            return false;
        }

        $totalXpInLevel = $nextLevelXp - $currentLevelXp;
        $xpInLevel = $this->getXp() - $currentLevelXp;

        return 1 / $totalXpInLevel * $xpInLevel;
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
        if ($uncapped) {
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
     * @param int $level
     *
     * @return HighscoreSkill
     */
    private function setLevel(int $level): HighscoreSkill
    {
        if ($level < 1) {
            $level = $this->getSkill()->getMinimumLevel();
        }

        $this->level = $level;

        return $this;
    }

    /**
     * @param int $xp
     *
     * @return HighscoreSkill
     */
    private function setXp(int $xp): HighscoreSkill
    {
        if ($xp < 0) {
            $xp = 0;
        }

        $this->xp = $xp;

        return $this;
    }

    /**
     * @param HighscoreEntry $entry
     * @return HighscoreEntryComparison
     */
    public function compareTo(HighscoreEntry $entry): HighscoreEntryComparison
    {
        if ($entry instanceof HighscoreSkill) {
            return new HighscoreSkillComparison($this, $entry);
        }

        return parent::compareTo($entry);
    }
}
