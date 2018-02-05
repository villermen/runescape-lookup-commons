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
        $attackLevel = $this->getSkill(Skill::SKILL_ATTACK) ? $this->getSkill(Skill::SKILL_ATTACK)->getLevel() : 1;
        $defenceLevel = $this->getSkill(Skill::SKILL_DEFENCE) ? $this->getSkill(Skill::SKILL_DEFENCE)->getLevel() : 1;
        $strengthLevel = $this->getSkill(Skill::SKILL_STRENGTH) ? $this->getSkill(Skill::SKILL_STRENGTH)->getLevel() : 1;
        $constitutionLevel = $this->getSkill(Skill::SKILL_CONSTITUTION) ? $this->getSkill(Skill::SKILL_CONSTITUTION)->getLevel() : 10;
        $rangedLevel = $this->getSkill(Skill::SKILL_RANGED) ? $this->getSkill(Skill::SKILL_RANGED)->getLevel() : 1;
        $prayerLevel = $this->getSkill(Skill::SKILL_PRAYER) ? $this->getSkill(Skill::SKILL_PRAYER)->getLevel() : 1;
        $magicLevel = $this->getSkill(Skill::SKILL_MAGIC) ? $this->getSkill(Skill::SKILL_MAGIC)->getLevel() : 1;
        $summoningLevel = $includeSummoning && $this->getSkill(Skill::SKILL_SUMMONING) ? $this->getSkill(Skill::SKILL_SUMMONING)->getLevel() : 1;

        return (int)((
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
