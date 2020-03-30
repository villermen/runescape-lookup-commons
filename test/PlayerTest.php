<?php

use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Activity;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\PlayerDataFetcher;
use Villermen\RuneScape\Skill;

class PlayerTest extends TestCase
{
    /**
     * Excl should have lifetime membership due to winning the first Machinima competition. Now let's hope they don't
     * turn their adventurer's log to private.
     */
    const PLAYER_NAME = "excl";
    const NONEXISTENT_PLAYER_NAME = "ifugMERWzm5G";

    /** @var PlayerDataFetcher */
    protected $dataFetcher;

    /** @var Player */
    protected $player;

    public function setUp(): void
    {
        $this->dataFetcher = new PlayerDataFetcher(10);
        $this->player = new Player(self::PLAYER_NAME, $this->dataFetcher);
    }

    public function testDataFetchingAndCaching(): void
    {
        $highScore = $this->player->getSkillHighScore();
        self::assertSame($highScore, $this->player->getSkillHighScore());
        self::assertGreaterThanOrEqual(1929, $highScore->getSkill(Skill::SKILL_TOTAL)->getLevel());
        self::assertGreaterThanOrEqual(9, $highScore->getSkill(Skill::SKILL_DIVINATION)->getLevel());

        $activityFeed = $this->dataFetcher->getCachedActivityFeed(self::PLAYER_NAME);
        self::assertNotNull($activityFeed);
        self::assertNotNull($this->dataFetcher->getCachedRealName(self::PLAYER_NAME));

        self::assertSame($activityFeed, $this->player->getActivityFeed());
        self::assertGreaterThan(new DateTime("2018-01-30"), $activityFeed->getItems()[0]->getTime());

        $oldSchoolActivityHighScore = $this->player->getOldSchoolActivityHighScore();
        self::assertSame($oldSchoolActivityHighScore, $this->player->getOldSchoolActivityHighScore());
        self::assertGreaterThanOrEqual(0, $oldSchoolActivityHighScore->getActivity(Activity::ACTIVITY_OLD_SCHOOL_MASTER_CLUE_SCROLLS)->getScore());

        $this->player->fixNameIfCached();
        self::assertEquals("Excl", $this->player->getName());
    }

    public function testNonExistentPlayer(): void
    {
        try {
            $player = new Player(self::NONEXISTENT_PLAYER_NAME, $this->dataFetcher);
            $player->fixName();

            self::fail();
        } catch (FetchFailedException $exception) {
            self::addToAssertionCount(1);
        }
    }
}
