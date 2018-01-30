<?php

use Villermen\RuneScape\Activity;
use Villermen\RuneScape\HighScore\HighScore;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Skill;

class HighScoreTest extends TestCase
{
    /** @var HighScore */
    private $highScore;

    public function setUp()
    {
        $this->highScore = new HighScore(new Player("Villermen"), file_get_contents(__DIR__ . "/fixtures/high-score1.csv"));
    }

    public function testEntries()
    {
        // Skill
        self::assertEquals(120, $this->highScore->getSkill(Skill::SKILL_INVENTION)->getLevel());
        self::assertEquals(99, $this->highScore->getSkill(Skill::SKILL_SMITHING)->getLevel());
        self::assertEquals(106, $this->highScore->getSkill(Skill::SKILL_SMITHING)->getLevel(true));
        self::assertEquals(2733, $this->highScore->getSkill(Skill::SKILL_TOTAL)->getLevel(true));
        self::assertNull($this->highScore->getSkill(200));

        $invSkill = $this->highScore->getSkill(Skill::SKILL_INVENTION);
        self::assertEquals(120, $invSkill->getLevel());
        self::assertEquals(123, $invSkill->getLevel(true));
        self::assertEquals(89610570, $invSkill->getXp());
        self::assertEquals(2402334, $invSkill->getXpToNextLevel());
        self::assertEquals(29334, $invSkill->getRank());
        self::assertSame(Skill::getSkill(Skill::SKILL_INVENTION), $invSkill->getSkill());

        // Activity
        self::assertEquals(1362803, $this->highScore->getActivity(Activity::ACTIVITY_DOMINION_TOWER)->getScore());
        self::assertNull($this->highScore->getActivity(200));
        self::assertFalse($this->highScore->getActivity(Activity::ACTIVITY_GIELINOR_GAMES_RESOURCE_RACE)->getRank());
        self::assertEquals(0, $this->highScore->getActivity(Activity::ACTIVITY_WORLD_EVENT_2_BANDOS_KILLS)->getScore());

    }

    public function testCombatLevel()
    {
        self::assertEquals(138, $this->highScore->getCombatLevel());
        self::assertEquals(126, $this->highScore->getCombatLevel(false));
        self::assertEquals(157, $this->highScore->getCombatLevel(true, true));
    }
}
