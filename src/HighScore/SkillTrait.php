<?php

namespace Villermen\RuneScape\HighScore;

trait SkillTrait
{
    public function getLevel(int $xp): int
    {
        return min($this->getVirtualLevel($xp), $this->getMaximumLevel());
    }

    public function getVirtualLevel(int $xp): int
    {
        $xpTable = $this->getXpTable();

        for ($level = 1; $level <= count($xpTable); $level++) {
            if ($xpTable[$level] > $xp) {
                $level--;
                break;
            }
        }

        return max($this->getMinimumLevel(), $level);
    }

    public function getXp(int $level): ?int
    {
        return $this->getXpTable()[$level] ?? null;
    }
}
