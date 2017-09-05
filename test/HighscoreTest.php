<?php

use Villermen\RuneScape\Constants;
use Villermen\RuneScape\Highscore\Highscore;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;

class HighscoreTest extends TestCase
{
    /** @var Highscore */
    private $highscore;

    public function setUp()
    {
        $this->highscore = new Highscore(new Player("Villermen"), file_get_contents(__DIR__ . "/fixtures/highscore.csv"));
    }

    public function testEntries()
    {
        self::assertEquals(120, $this->highscore->getSkill(Constants::SKILL_INVENTION)->getLevel());
        self::assertEquals(99, $this->highscore->getSkill(Constants::SKILL_SMITHING)->getLevel());
        self::assertEquals(106, $this->highscore->getSkill(Constants::SKILL_SMITHING)->getLevel(true));

        self::assertEquals(1362803, $this->highscore->getActivity(Constants::ACTIVITY_DOMINION_TOWER)->getScore());
    }

    public function testCombatLevel()
    {
        self::assertEquals(138, $this->highscore->getCombatLevel());
        self::assertEquals(126, $this->highscore->getCombatLevel(false));
        self::assertEquals(155, $this->highscore->getCombatLevel(true, true));
    }
}
