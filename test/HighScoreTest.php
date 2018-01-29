<?php

use Villermen\RuneScape\Constants;
use Villermen\RuneScape\HighScore\HighScore;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;

class HighScoreTest extends TestCase
{
    /** @var HighScore */
    private $highScore;

    public function setUp()
    {
        $this->highScore = new HighScore(new Player("Villermen"), file_get_contents(__DIR__ . "/fixtures/high-score.csv"));
    }

    public function testEntries()
    {
        self::assertEquals(120, $this->highScore->getSkill(Constants::SKILL_INVENTION)->getLevel());
        self::assertEquals(99, $this->highScore->getSkill(Constants::SKILL_SMITHING)->getLevel());
        self::assertEquals(106, $this->highScore->getSkill(Constants::SKILL_SMITHING)->getLevel(true));
        self::assertNull($this->highScore->getSkill(200));

        self::assertEquals(1362803, $this->highScore->getActivity(Constants::ACTIVITY_DOMINION_TOWER)->getScore());
    }

    public function testCombatLevel()
    {
        self::assertEquals(138, $this->highScore->getCombatLevel());
        self::assertEquals(126, $this->highScore->getCombatLevel(false));
        self::assertEquals(155, $this->highScore->getCombatLevel(true, true));
    }
}
