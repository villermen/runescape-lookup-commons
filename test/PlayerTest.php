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

        self::markTestSkipped();
    }
}
