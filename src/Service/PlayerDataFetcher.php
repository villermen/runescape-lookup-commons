<?php

namespace Villermen\RuneScape\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\ActivityFeed\ActivityFeedItem;
use Villermen\RuneScape\Exception\DataConversionException;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\HighScore\HighScore;
use Villermen\RuneScape\HighScore\OsrsHighScore;
use Villermen\RuneScape\HighScore\Rs3HighScore;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\PlayerData\AdventurersLogData;
use Villermen\RuneScape\PlayerData\RuneMetricsData;

/**
 * Fetches and converts external API data to usable objects.
 */
class PlayerDataFetcher
{
    protected const RS3_INDEX_LITE_URL = "https://secure.runescape.com/m=hiscore/index_lite.ws?player=%s";
    protected const OSRS_INDEX_LITE_URL = "https://secure.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s";
    protected const ADVENTURERS_LOG_URL = "https://secure.runescape.com/m=adventurers-log/rssfeed?searchName=%s";
    protected const RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20";

    protected readonly HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? HttpClient::create([
            'timeout' => 5,
            'max_redirects' => 0,
        ]);
    }

    /**
     * The tried and true way to obtain highscore data.
     *
     * @throws FetchFailedException
     * @throws DataConversionException
     */
    public function fetchIndexLite(Player $player, bool $oldSchool = false): OsrsHighScore|Rs3HighScore
    {
        $url = $oldSchool ? self::OSRS_INDEX_LITE_URL : self::RS3_INDEX_LITE_URL;
        $url = sprintf($url, urlencode($player->getName()));
        $data = $this->fetchUrl($url);

        $entries = explode("\n", trim($data));

        $skills = [];
        $activities = [];
        foreach ($entries as $entry) {
            $entryArray = explode(',', $entry);

            // Skills
            if (count($entryArray) === 3) {
                [$rank, $level, $xp] = $entryArray;
                $skills[] = [
                    'rank' => HighScore::correctValue($rank),
                    'level' => HighScore::correctValue($level),
                    'xp' => HighScore::correctValue($xp),
                ];
                continue;
            }

            // Activities
            if (count($entryArray) === 2) {
                [$rank, $score] = $entryArray;
                $activities[] = [
                    'rank' => HighScore::correctValue($rank),
                    'score' => HighScore::correctValue($score),
                ];
                continue;
            }

            throw new DataConversionException('Invalid high score data supplied.');
        }

        return $oldSchool ? new OsrsHighScore($skills, $activities) : new Rs3HighScore($skills, $activities);
    }

    /**
     * May yield unranked and non-member RS3 highscore data, but lacks highscore activities.
     *
     * @throws FetchFailedException
     * @throws DataConversionException
     */
    public function fetchRuneMetrics(Player $player): RuneMetricsData
    {
        $url = sprintf(self::RUNEMETRICS_URL, urlencode($player->getName()));
        $data = @json_decode($this->fetchUrl($url), associative: true);

        if (!$data) {
            throw new DataConversionException('Could not decode RuneMetrics API response.');
        }

        if (isset($data['error'])) {
            throw new FetchFailedException('RuneMetrics API returned an error. User might not exist.');
        }

        // HighScore
        $totalRank = isset($data['rank']) ? (int)str_replace(',', '', $data['rank']) : null;

        $skills = [
            0 => [
                'rank' => $totalRank,
                'level' => $data['totalskill'],
                'xp' => $data['totalxp'],
            ]
        ];

        foreach($data['skillvalues'] as $skillvalue) {
            // +1 because total is not considered a skill.
            $skillId = $skillvalue['id'] + 1;
            $skills[$skillId] = [
                'rank' =>  $skillvalue['rank'] ?? null,
                'level' => $skillvalue['level'],
                'xp' => (int)($skillvalue['xp'] / 10),
            ];
        }

        ksort($skills);

        // ActivityFeed
        $activities = [];
        foreach($data['activities'] as $activity) {
            $time = new \DateTimeImmutable($activity['date'], new \DateTimeZone('UTC'));

            $activities[] = new ActivityFeedItem(
                $time,
                trim($activity['text']),
                trim($activity['details'])
            );
        }

        return new RuneMetricsData(
            $player,
            $data['name'],
            new Rs3HighScore($skills, activities: []),
            new ActivityFeed($activities),
        );
    }

    /**
     * @throws DataConversionException
     * @throws FetchFailedException
     */
    public function fetchAdventurersLog(Player $player): AdventurersLogData
    {
        $url = sprintf(self::ADVENTURERS_LOG_URL, urlencode($player->getName()));
        $data = $this->fetchUrl($url);

        // Parse data into ActivityFeed object
        $feedItems = [];

        try {
            $feed = new \SimpleXmlElement($data);
        } catch (\Exception) {
            throw new DataConversionException('Could not parse the adventurer\'s log as XML.');
        }

        $itemElements = @$feed->xpath('/rss/channel/item');
        if (!$itemElements) {
            throw new DataConversionException('Could not obtain any feed items from feed.');
        }

        foreach ($itemElements as $itemElement) {
            $time = new \DateTimeImmutable($itemElement->pubDate, new \DateTimeZone('UTC'));
            $title = trim((string)$itemElement->title);
            $description = trim((string)$itemElement->description);

            if (!$title || !$description) {
                throw new DataConversionException(sprintf(
                    'Could not parse one of the activity feed items. (time: %s, title: %s, description: %s)',
                    $time->format('j-n-Y'), $title, $description
                ));
            }

            $feedItems[] = new ActivityFeedItem($time, $title, $description);
        }

        // Parse real name
        $titleElements = @$feed->xpath('/rss/channel/title');
        if (!$titleElements) {
            throw new DataConversionException('Could not obtain player name element from feed.');
        }

        $title = (string)$titleElements[0];
        $realName = trim(substr($title, strrpos($title, ':') + 1));

        return new AdventurersLogData(
            $player,
            $realName,
            new ActivityFeed($feedItems),
        );
    }

    /**
     * Fetches data from the given URL.
     *
     * @throws FetchFailedException
     */
    protected function fetchUrl(string $url): string
    {
        try {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->getContent();
            if (!$data) {
                throw new FetchFailedException(sprintf('URL \"%s\" returned no data.', $url));
            }

            return $data;
        } catch (ExceptionInterface $exception) {
            throw new FetchFailedException(sprintf("An exception occurred while fetching \"%s\": %s", $url, $exception->getMessage()), previous: $exception);
        }
    }
}
