<?php

namespace Villermen\RuneScape\HighScore;

use Villermen\RuneScape\RuneScapeException;
use Villermen\RuneScape\Skill;

class HighScoreSkillComparison extends HighScoreEntryComparison
{
    /** @var int */
    protected $levelDifference = false;

    /** @var int */
    protected $xpDifference = false;

    /** @var Skill */
    protected $skill;

    // TODO: Change
    public function __construct(HighScoreSkill $skill1, HighScoreSkill $skill2, bool $uncapped = false)
    {
        if ($skill1->getSkill() !== $skill2->getSkill()) {
            throw new RuneScapeException("Can't compare two different highscore skills.");
        }

        $this->skill = $skill1->getSkill();

        parent::__construct($skill1, $skill2);

        $this->levelDifference = $skill1->getLevel($uncapped) - $skill2->getLevel($uncapped);
        $this->xpDifference = $skill1->getXp() - $skill2->getXp();
    }

    /**
     * @return int
     */
    public function getLevelDifference(): int
    {
        return $this->levelDifference;
    }

    /**
     * @return int
     */
    public function getXpDifference(): int
    {
        return $this->xpDifference;
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }
}
