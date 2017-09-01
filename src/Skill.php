<?php

namespace Villermen\RuneScape;

class Skill
{
    /** @var string */
    private $name;

    /** @var bool */
    private $highLevelCap;

    /** @var bool */
    private $elite;

    public function __construct(string $name, bool $highLevelCap = false, bool $elite = false)
    {
        $this->name = $name;
        $this->highLevelCap = $highLevelCap;
        $this->elite = $elite;
    }

    /**
     * Returns the level in this skill for the given XP.
     *
     * @param int $xp
     * @param bool $uncapped
     * @return int
     * @throws RuneScapeException
     */
    public function getLevel(int $xp, bool $uncapped = false): int
    {
        $xpTable = $this->getXpTable();

        for ($level = 1; $level <= count($xpTable); $level++) {
            if ($xpTable[$level] > $xp) {
                $level--;
                break;
            }
        }

        // Cap
        if (!$uncapped) {
            $levelCap = $this->isHighLevelCap() ? 120 : 99;

            if ($level > $levelCap) {
                $level = $levelCap;
            }
        }

        return $level;
    }

    /**
     * Returns the XP corresponding to the given level of this skill.
     * Returns false if the level is too high or low to be represented.
     *
     * @param int $level
     * @return int|false
     */
    public function getXp(int $level)
    {
        $xpTable = $this->getXpTable();

        if (!isset($xpTable[$level])) {
            return false;
        }

        return $this->getXpTable()[$level];
    }

    /**
     * @return int[]
     */
    public function getXpTable(): array
    {
        return $this->isElite() ? Constants::ELITE_XP_TABLE : Constants::XP_TABLE;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isHighLevelCap(): bool
    {
        return $this->highLevelCap;
    }

    /**
     * @return bool
     */
    public function isElite(): bool
    {
        return $this->elite;
    }
}
