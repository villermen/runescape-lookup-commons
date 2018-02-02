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
        $this->player = new Player("excl", new PlayerDataFetcher(10));
    }

    public function testPlayer()
    {
        $highScore = $this->player->getHighScore();
        self::assertSame($highScore, $this->player->getHighScore());

        $activityFeed = $this->player->getActivityFeed();
        self::assertSame($activityFeed, $this->player->getActivityFeed());

        $oldSchoolHighScore = $this->player->getOldSchoolHighScore();
        self::assertSame($oldSchoolHighScore, $this->player->getOldSchoolHighScore());

        $this->player->fixName();
        self::assertEquals("Excl", $this->player->getName());


        // TODO: HighScore wrong index for skills

        // TODO: Test certain properties of objects (activityfeed latest date, highscore xp above x, oldschool dito)
    }
}
