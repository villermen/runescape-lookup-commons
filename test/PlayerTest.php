<?php

use Villermen\RuneScape\Constants;
use Villermen\RuneScape\Highscore\HighscoreActivity;
use Villermen\RuneScape\Highscore\HighscoreSkill;
use Villermen\RuneScape\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var Player */
    private static $player;

    /** @inheritdoc */
    public static function setUpBeforeClass()
    {
        self::$player = new Player("Villermen");
    }

    public function testGetHighscore()
    {
        $highscore = self::$player->getHighscore();

        foreach($highscore as $item) {
            if ($item instanceof HighscoreSkill) {
                if ($item->getSkill()->getId() === Constants::SKILL_INVENTION) {
                    self::assertEquals(120, $item->getLevel());
                }

                if ($item->getSkill()->getId() === Constants::SKILL_SMITHING) {
                    self::assertEquals(99, $item->getLevel());
                    self::assertGreaterThan(99, $item->getLevel(true));
                }
            }

            if ($item instanceof HighscoreActivity) {
                if ($item->getActivity()->getId() === Constants::ACTIVITY_DOMINION_TOWER) {
                    self::assertGreaterThan(1300000, $item->getScore());
                }
            }
        }
    }

    // TODO: Test comparing
    // TODO: Test combat level generation
    // TODO: Test activity feed somehow
}
