<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Skill;

class SkillHighScore
{
    /** @var HighScoreSkill[]  */
    protected $skills;

    /**
     * Creates a HighScore object from raw high score data.
     *
     * @param HighScoreSkill[] $skills
     */
    public function __construct(array $skills)
    {
        $this->skills = [];
        foreach($skills as $skill) {
            $this->skills[$skill->getSkill()->getId()] = $skill;
        }

        ksort($this->skills);
    }

    /**
     * Returns the combat level of this high score.
     *
     * @param bool $includeSummoning Whether to include the summoning skill while calculating the combat level.
     * @param bool $uncapped
     * @return int
     */
    public function getCombatLevel($includeSummoning = true, $uncapped = false): int
    {
        $attackLevel = $this->getSkill(Skill::SKILL_ATTACK)->getLevel($uncapped);
        $defenceLevel = $this->getSkill(Skill::SKILL_DEFENCE)->getLevel($uncapped);
        $strengthLevel = $this->getSkill(Skill::SKILL_STRENGTH)->getLevel($uncapped);
        $constitutionLevel = $this->getSkill(Skill::SKILL_CONSTITUTION)->getLevel($uncapped);
        $rangedLevel = $this->getSkill(Skill::SKILL_RANGED)->getLevel($uncapped);
        $prayerLevel = $this->getSkill(Skill::SKILL_PRAYER)->getLevel($uncapped);
        $magicLevel = $this->getSkill(Skill::SKILL_MAGIC)->getLevel($uncapped);

        $summoningSkill = $this->getSkill(Skill::SKILL_SUMMONING);

        if ($includeSummoning && $summoningSkill) {
            $summoningLevel = $summoningSkill->getLevel($uncapped);
        } else {
            $summoningLevel = 1;
        }

        return (int)((
            max($attackLevel + $strengthLevel, $magicLevel * 2, $rangedLevel * 2) * 1.3 +
            $defenceLevel + $constitutionLevel +
            floor($prayerLevel / 2) + floor($summoningLevel / 2)
        ) / 4);
    }

    /**
     * @return HighScoreSkill[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param $id
     * @return HighScoreSkill|null
     */
    public function getSkill($id)
    {
        if (!isset($this->skills[$id])) {
            return null;
        }

        return $this->skills[$id];
    }

    /**
     * Creates a HighScoreComparison between this high score and the given high score.
     *
     * @param SkillHighScore $otherHighScore
     * @return SkillHighScoreComparison
     */
    public function compareTo(SkillHighScore $otherHighScore): SkillHighScoreComparison
    {
        return new SkillHighScoreComparison($this, $otherHighScore);
    }
}
