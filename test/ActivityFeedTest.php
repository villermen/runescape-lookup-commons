<?php

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use PHPUnit\Framework\TestCase;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\PlayerDataConverter;

class ActivityFeedTest extends TestCase
{
    /** @var string */
    protected $realName1;

    /** @var ActivityFeed */
    protected $activityFeed1;

    /** @var string */
    protected $realName2;

    /** @var ActivityFeed */
    protected $activityFeed2;

    public function setUp()
    {
        $dataConverter = new PlayerDataConverter();

        $convertedData1 = $dataConverter->convertAdventurersLog(file_get_contents(__DIR__ . "/fixtures/activity-feed-test-adventurers-log.xml"));
        $this->realName1 = $convertedData1[PlayerDataConverter::KEY_REAL_NAME];
        $this->activityFeed1 = $convertedData1[PlayerDataConverter::KEY_ACTIVITY_FEED];

        $convertedData2 = $dataConverter->convertRuneMetrics(file_get_contents(__DIR__ . "/fixtures/activity-feed-test-runemetrics.json"));
        $this->realName2 = $convertedData2[PlayerDataConverter::KEY_REAL_NAME];
        $this->activityFeed2 = $convertedData2[PlayerDataConverter::KEY_ACTIVITY_FEED];
    }

    public function testRealName()
    {
        self::assertEquals("Ardanwen", $this->realName1);
        self::assertEquals("Ardanwen", $this->realName2);
    }

    public function testGetItems()
    {
        self::assertCount(20, $this->activityFeed1->getItems());
        self::assertCount(11, $this->activityFeed2->getItems());

        self::assertEquals("Quest complete: The Jack of Spades", $this->activityFeed1->getItems()[19]->getTitle());
        self::assertStringStartsWith("I've", $this->activityFeed1->getItems()[19]->getDescription());
        self::assertEquals(new DateTime("2017-08-17 0:00", new DateTimeZone("UTC")), $this->activityFeed1->getItems()[19]->getTime());
    }

    public function testGetItemsAfter()
    {
        self::assertCount(5, $this->activityFeed2->getItemsAfter($this->activityFeed1->getItems()[0]));
    }

    public function testMerge()
    {
        $mergedActivityFeed = $this->activityFeed1->merge($this->activityFeed2);
        self::assertNotSame($this->activityFeed1, $mergedActivityFeed);
        self::assertCount(25, $mergedActivityFeed->getItems());
    }
}
