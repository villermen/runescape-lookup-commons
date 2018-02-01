<?php

use Villermen\RuneScape\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var Player */
    private $player;

    public function setUp()
    {
        // Excl should have lifetime membership due to winning the first Machinima competition
        // Now let's hope they don't turn their adventurer's log to private
        $this->player = new Player("Excl");
    }

    public function testGetHighScore()
    {
        $highScore = $this->player->getHighScore(false, 10);
        $oldSchoolHighScore = $this->player->getHighScore(true, 10);

        // Test caching
        self::assertSame($highScore, $this->player->getHighScore());
        self::assertSame($oldSchoolHighScore, $this->player->getHighScore(true));
    }

    public function testGetActivityFeed()
    {
        $activityFeed = $this->player->getActivityFeed(10);

        // Test caching
        self::assertSame($activityFeed, $this->player->getActivityFeed());
    }
}
