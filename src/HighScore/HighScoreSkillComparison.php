<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\Exception\RuneScapeException;
use Villermen\RuneScape\Skill;

class HighScoreSkillComparison extends HighScoreEntryComparison
{
    /** @var int */
    protected $rankDifference;

    /** @var int */
    protected $levelDifference;

    /** @var int */
    protected $xpDifference;

    /** @var HighScoreSkill */
    protected $skill1;

    /** @var HighScoreSkill */
    protected $skill2;

    /**
     * HighScoreComparisonSkill constructor.
     * @param HighScoreSkill|null $skill1
     * @param HighScoreSkill|null $skill2
     * @throws RuneScapeException
     */
    public function __construct($skill1, $skill2)
    {
        parent::__construct($skill1, $skill2);

        if (!$skill1) {
            $skill1 = new HighScoreSkill($skill2->getSkill(), -1, -1, -1);
        }

        if (!$skill2) {
            $skill2 = new HighScoreSkill($skill1->getSkill(), -1, -1, -1);
        }

        if ($skill1->getSkill() !== $skill2->getSkill()) {
            throw new RuneScapeException("Can't compare two different skills.");
        }

        $this->skill1 = $skill1;
        $this->skill2 = $skill2;
    }

    /**
     * @param bool $uncapped
     * @return int
     */
    public function getLevelDifference(bool $uncapped = false): int
    {
        return $this->skill1->getLevel($uncapped) - $this->skill2->getLevel($uncapped);
    }

    /**
     * @return int
     */
    public function getXpDifference(): int
    {
        return $this->skill1->getXp() - $this->skill2->getXp();
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill1->getSkill();
    }
}
