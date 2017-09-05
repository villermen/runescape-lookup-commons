<?php

use Villermen\RuneScape\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var Player */
    private static $player;

    /** @inheritdoc */
    public static function setUpBeforeClass()
    {
        // Use static so that caching works across tests
        self::$player = new Player("Villermen");
    }

    public function testGetHighscore()
    {
        self::$player->getHighscore();
        self::$player->getHighscore(true);

        // Will prevent this test from being marked as risky for having no assertions
        $this->addToAssertionCount(1);
    }

    // TODO: Testing getActivityFeed() would be desired, but the feed vanishes when membership expires
}
