<?php

namespace Villermen\RuneScape\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\HighScore\OsrsActivity;
use Villermen\RuneScape\HighScore\OsrsSkill;
use Villermen\RuneScape\HighScore\Rs3Activity;
use Villermen\RuneScape\HighScore\Rs3Skill;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Service\PlayerDataFetcher;

class PlayerDataFetcherTest extends TestCase
{
    /**
     * Excl should have lifetime membership due to winning the first Machinima competition. Now let's hope they don't
     * turn their adventurer's log to private.
     */
    private const MEMBER_PLAYER_NAME = 'excl';
    private const NONEXISTENT_PLAYER_NAME = 'ifugMERWzm5G';

    /** @return mixed[] */
    public static function indexLiteRs3Provider(): array
    {
        return [
            'file' => [__DIR__ . '/fixtures/index-lite-rs3.csv'],
            'live' => ['live'],
        ];
    }

    /** @return mixed[] */
    public static function indexLiteOsrsProvider(): array
    {
        return [
            'file' => [__DIR__ . '/fixtures/index-lite-osrs.csv'],
            'live' => ['live'],
        ];
    }

    /** @return mixed[] */
    public static function runeMetricsProvider(): array
    {
        return [
            'file' => [__DIR__ . '/fixtures/runemetrics.json'],
            'live' => ['live'],
        ];
    }

    /** @return mixed[] */
    public static function adventurersLogProvider(): array
    {
        return [
            'file' => [__DIR__ . '/fixtures/adventurers-log.xml'],
            'live' => ['live'],
        ];
    }

    #[DataProvider('indexLiteRs3Provider')]
    public function testFetchIndexLiteRs3(string $source): void
    {
        $player = new Player(self::MEMBER_PLAYER_NAME);
        $dataFetcher = $this->createDataFetcher($source);

        $highScore = $dataFetcher->fetchIndexLite($player);
        self::assertGreaterThanOrEqual(1982, $highScore->getSkill(Rs3Skill::TOTAL)->getLevel());
        self::assertGreaterThanOrEqual(187952, $highScore->getSkill(Rs3Skill::DIVINATION)->getXp());
        self::assertEquals(1391, $highScore->getActivity(Rs3Activity::CONQUEST)->getScore());
    }

    #[DataProvider('indexLiteOsrsProvider')]
    public function testFetchIndexLiteOsrs(string $source): void
    {
        $player = new Player(self::MEMBER_PLAYER_NAME);
        $dataFetcher = $this->createDataFetcher($source);

        $highScore = $dataFetcher->fetchIndexLite($player, oldSchool: true);
        self::assertGreaterThanOrEqual(1882, $highScore->getSkill(OsrsSkill::TOTAL)->getLevel());
        self::assertGreaterThanOrEqual(3644543, $highScore->getSkill(OsrsSkill::CONSTRUCTION)->getXp());
        self::assertGreaterThanOrEqual(71, $highScore->getActivity(OsrsActivity::ZULRAH)->getScore());
    }

    #[DataProvider('runeMetricsProvider')]
    public function testFetchRuneMetrics(string $source): void
    {
        $player = new Player(self::MEMBER_PLAYER_NAME);
        $dataFetcher = $this->createDataFetcher($source);

        $runeMetrics = $dataFetcher->fetchRuneMetrics($player);
        self::assertEquals('Excl', $runeMetrics->displayName);
        // You would expect this to equal the index lite value, but it doesn't...
        self::assertGreaterThanOrEqual(1983, $runeMetrics->highScore->getSkill(Rs3Skill::TOTAL)->getLevel());
        self::assertEquals(187952, $runeMetrics->highScore->getSkill(Rs3Skill::DIVINATION)->getXp());
        // RuneMetrics does not include activities in high score.
        self::assertNull($runeMetrics->highScore->getActivity(Rs3Activity::CONQUEST)->getScore());
        self::assertCount(20, $runeMetrics->activityFeed->getItems());
        self::assertGreaterThanOrEqual(new \DateTime('2022-09-05 04:13:00', new \DateTimeZone('UTC')), $runeMetrics->activityFeed->getItems()[0]->getTime());
    }

    #[DataProvider('adventurersLogProvider')]
    public function testFetchAdventurersLog(string $source): void
    {
        $player = new Player(self::MEMBER_PLAYER_NAME);
        $dataFetcher = $this->createDataFetcher($source);

        $adventurersLog = $dataFetcher->fetchAdventurersLog($player);
        self::assertEquals('Excl', $adventurersLog->displayName);
        self::assertCount(20, $adventurersLog->activityFeed->getItems());
        // Adventurer's log does not include times, but only for older events...
        self::assertGreaterThanOrEqual(new \DateTime('2022-09-05 00:00:00', new \DateTimeZone('UTC')), $adventurersLog->activityFeed->getItems()[0]->getTime());
    }

    public function testNonExistentPlayer(): void
    {
        $player = new Player(self::NONEXISTENT_PLAYER_NAME);
        $dataFetcher = $this->createDataFetcher('live');

        try {
            $dataFetcher->fetchIndexLite($player);
            self::fail();
        } catch (FetchFailedException) {
            self::addToAssertionCount(1);
        }

        try {
            $dataFetcher->fetchRuneMetrics($player);
            self::fail();
        } catch (FetchFailedException) {
            self::addToAssertionCount(1);
        }

        try {
            $dataFetcher->fetchAdventurersLog($player);
            self::fail();
        } catch (FetchFailedException) {
            self::addToAssertionCount(1);
        }
    }

    private function createDataFetcher(string $source): PlayerDataFetcher
    {
        if ($source === 'live') {
            return new PlayerDataFetcher();
        }

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')->willReturn(file_get_contents($source));

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        return new PlayerDataFetcher($httpClientMock);
    }
}
