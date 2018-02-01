<?php

use Villermen\RuneScape\Player;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\PlayerDataFetcher;

class PlayerTest extends TestCase
{
    /** @var Player */
    private $player;

    public function setUp()
    {
        // Excl should have lifetime membership due to winning the first Machinima competition
        // Now let's hope they don't turn their adventurer's log to private
        $this->player = new Player("Excl", new PlayerDataFetcher(10));
    }

    public function testGetHighScore()
    {
        $highScore = $this->player->getHighScore();
        $oldSchoolHighScore = $this->player->getOldSchoolHighScore();

        // Test caching
        self::assertSame($highScore, $this->player->getHighScore());
        self::assertSame($oldSchoolHighScore, $this->player->getOldSchoolHighScore());
    }

    public function testGetActivityFeed()
    {
        $activityFeed = $this->player->getActivityFeed();

        // Test caching
        self::assertSame($activityFeed, $this->player->getActivityFeed());
    }
}
