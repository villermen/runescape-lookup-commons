<?php

namespace Villermen\RuneScape\HighScore;

class SkillHighScoreComparison
{
    /** @var HighScoreSkillComparison[] */
    protected $skills = [];

    public function __construct(SkillHighScore $highScore1, SkillHighScore $highScore2)
    {
        $skillIds = array_unique(array_merge(
            array_keys($highScore1->getSkills()),
            array_keys($highScore2->getSkills())
        ));

        foreach($skillIds as $skillId) {
            $this->skills[$skillId] = new HighScoreSkillComparison(
                $highScore1->getSkill($skillId),
                $highScore2->getSkill($skillId)
            );
        }
    }

    /**
     * @return HighScoreSkillComparison[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    public function getSkill(int $id): HighScoreSkillComparison
    {
        if (!isset($this->skills[$id])) {
            throw new \InvalidArgumentException("Neither of the high scores contains the requested skill.");
        }

        return $this->skills[$id];
    }
}
