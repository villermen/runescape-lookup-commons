<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Skill;

class HighScoreSkillComparison extends HighScoreEntryComparison
{
    /** @var Skill */
    protected $skill;

    /** @var int */
    protected $levelDifference;

    /** @var int */
    protected $virtualLevelDifference;

    /** @var int */
    protected $xpDifference;

    public function __construct(?HighScoreSkill $skill1, ?HighScoreSkill $skill2)
    {
        parent::__construct($skill1, $skill2);

        if (!$skill1) {
            $skill1 = new HighScoreSkill($skill2->getSkill(), -1, -1, -1);
        }

        if (!$skill2) {
            $skill2 = new HighScoreSkill($skill1->getSkill(), -1, -1, -1);
        }

        if ($skill1->getSkill() !== $skill2->getSkill()) {
            throw new \InvalidArgumentException("Can't compare two different skills.");
        }

        $this->skill = $skill1->getSkill();
        $this->xpDifference = $skill1->getXp() - $skill2->getXp();
        $this->levelDifference = $skill1->getLevel() - $skill2->getLevel();
        $this->virtualLevelDifference = $skill1->getVirtualLevel() - $skill2->getVirtualLevel();
    }

    public function getSkill(): Skill
    {
        return $this->skill;
    }

    public function getLevelDifference(): int
    {
        return $this->levelDifference;
    }

    public function getVirtualLevelDifference(): int
    {
        return $this->virtualLevelDifference;
    }

    public function getXpDifference(): int
    {
        return $this->xpDifference;
    }
}
