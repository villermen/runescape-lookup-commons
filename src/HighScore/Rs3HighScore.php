<?php

namespace Villermen\RuneScape\HighScore;

/**
 * @extends HighScore<Rs3Skill, Rs3Activity>
 */
class Rs3HighScore extends HighScore
{
    public function getSkills(): array
    {
        return array_map($this->getSkill(...), Rs3Skill::cases());
    }

    public function getActivities(): array
    {
        return array_map($this->getActivity(...), Rs3Activity::cases());
    }

    public function getCombatLevel(bool $includeSummoning = true): float
    {
        $attackLevel = $this->getSkill(Rs3Skill::ATTACK)->getLevelOrMinimum();
        $defenceLevel = $this->getSkill(Rs3Skill::DEFENCE)->getLevelOrMinimum();
        $strengthLevel = $this->getSkill(Rs3Skill::STRENGTH)->getLevelOrMinimum();
        $constitutionLevel = $this->getSkill(Rs3Skill::CONSTITUTION)->getLevelOrMinimum();
        $rangedLevel = $this->getSkill(Rs3Skill::RANGED)->getLevelOrMinimum();
        $prayerLevel = $this->getSkill(Rs3Skill::PRAYER)->getLevelOrMinimum();
        $magicLevel = $this->getSkill(Rs3Skill::MAGIC)->getLevelOrMinimum();
        $summoningLevel = $includeSummoning ? $this->getSkill(Rs3Skill::SUMMONING)->getLevelOrMinimum() : 1;
        $necromancyLevel = $this->getSkill(Rs3Skill::NECROMANCY)->getLevelOrMinimum();

        return ((
            max($attackLevel + $strengthLevel, $magicLevel * 2, $rangedLevel * 2, $necromancyLevel * 2) * 1.3 +
            $defenceLevel + $constitutionLevel + floor($prayerLevel / 2) + floor($summoningLevel / 2)
        ) / 4);
    }
}
