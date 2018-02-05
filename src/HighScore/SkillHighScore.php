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
     * @return int
     */
    public function getCombatLevel($includeSummoning = true): int
    {
        $attackLevel = $this->getSkill(Skill::SKILL_ATTACK)->getLevel();
        $defenceLevel = $this->getSkill(Skill::SKILL_DEFENCE)->getLevel();
        $strengthLevel = $this->getSkill(Skill::SKILL_STRENGTH)->getLevel();
        $constitutionLevel = $this->getSkill(Skill::SKILL_CONSTITUTION)->getLevel();
        $rangedLevel = $this->getSkill(Skill::SKILL_RANGED)->getLevel();
        $prayerLevel = $this->getSkill(Skill::SKILL_PRAYER)->getLevel();
        $magicLevel = $this->getSkill(Skill::SKILL_MAGIC)->getLevel();

        $summoningSkill = $this->getSkill(Skill::SKILL_SUMMONING);

        if ($includeSummoning && $summoningSkill) {
            $summoningLevel = $summoningSkill->getLevel();
        } else {
            $summoningLevel = 1;
        }

        return (int)max(3, (
            max($attackLevel + $strengthLevel, $magicLevel * 2, $rangedLevel * 2) * 1.3 +
            $defenceLevel + $constitutionLevel + floor($prayerLevel / 2) + floor($summoningLevel / 2)
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
