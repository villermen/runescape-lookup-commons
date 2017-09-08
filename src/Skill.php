<?php

namespace Villermen\RuneScape;

class Skill
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $highLevelCap;

    /** @var bool */
    protected $elite;

    /** @var int */
    protected $minimumLevel;

    /**
     * @param int $id
     * @param string $name
     * @param bool $highLevelCap
     * @param bool $elite
     */
    public function __construct(int $id, string $name, bool $highLevelCap = false, bool $elite = false, int $minimumLevel = 1)
    {
        $this->id = $id;
        $this->name = $name;
        $this->highLevelCap = $highLevelCap;
        $this->elite = $elite;
        $this->minimumLevel = $minimumLevel;
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMinimumLevel(): int
    {
        return $this->minimumLevel;
    }
}
