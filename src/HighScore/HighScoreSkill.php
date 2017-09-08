<?php

namespace Villermen\RuneScape\HighScore;

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
     * @return HighScoreSkill
     */
    private function setLevel(int $level): HighScoreSkill
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
     * @return HighScoreSkill
     */
    private function setXp(int $xp): HighScoreSkill
    {
        if ($xp < 0) {
            $xp = 0;
        }

        $this->xp = $xp;

        return $this;
    }

    /**
     * @param HighScoreEntry $entry
     * @return HighScoreEntryComparison|HighScoreSkillComparison
     */
    public function compareTo(HighScoreEntry $entry): HighScoreEntryComparison
    {
        if ($entry instanceof HighScoreSkill) {
            return new HighScoreSkillComparison($this, $entry);
        }

        return parent::compareTo($entry);
    }
}
