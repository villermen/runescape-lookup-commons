<?php

use Villermen\RuneScape\Constants;
use Villermen\RuneScape\HighscoreActivity;
use Villermen\RuneScape\HighscoreSkill;
use Villermen\RuneScape\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var Player */
    private $player;

    /** @inheritdoc */
    protected function setUp()
    {
        $this->player = new Player("Villermen");
    }

    public function testGetHighscore()
    {
        $highscore = $this->player->getHighscore();

        foreach($highscore as $item) {
            if ($item instanceof HighscoreSkill) {
                if ($item->getSkill() === Constants::getSkill("invention")) {
                    self::assertEquals(120, $item->getLevel());
                }

                if ($item->getSkill() === Constants::getSkill("smithing")) {
                    self::assertEquals(99, $item->getLevel());
                    self::assertGreaterThan(99, $item->getLevel(true));
                }
            }

            if ($item instanceof HighscoreActivity) {
                if ($item->getActivity() === Constants::getActivity("dominion tower")) {
                    self::assertGreaterThan(1300000, $item->getScore());
                }
            }
        }
    }
}
