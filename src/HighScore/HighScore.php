<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Skill;

class HighScore
{
    /** @var HighScoreSkill[]  */
    protected $skills = [];

    /** @var HighScoreActivity[] */
    protected $activities = [];

    /**
     * Creates a HighScore object from raw high score data.
     *
     * @param HighScoreSkill[] $skills
     * @param HighScoreActivity[] $activities
     */
    public function __construct(array $skills, array $activities)
    {
        // Ensure that skill and activities have their id as array key
        array_walk($skills, function(HighscoreSkill $skill, &$key) {
            $key = $skill->getSkill()->getId();
        });
        array_walk($activities, function(HighScoreActivity $activity, &$key) {
            $key = $activity->getActivity()->getId();
        });

        $this->skills = $skills;
        $this->activities = $activities;
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
     * @return HighScoreActivity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
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
     * @param $id
     * @return HighScoreActivity|null
     */
    public function getActivity($id)
    {
        if (!isset($this->activities[$id])) {
            return null;
        }

        return $this->activities[$id];
    }

    /**
     * Creates a HighScoreComparison between this high score and the given high score.
     *
     * @param HighScore $otherHighScore
     * @return HighScoreComparison
     */
    public function compareTo(HighScore $otherHighScore): HighScoreComparison
    {
        return new HighScoreComparison($this, $otherHighScore);
    }
}
