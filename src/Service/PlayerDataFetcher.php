<?php

namespace Villermen\RuneScape\Service;

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
    protected const ADVENTURERS_LOG_URL = "http://services.runescape.com/m=adventurers-log/rssfeed?searchName=%s";
    protected const RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20";

    /**
     * @param int $timeout The timeout used for every external request by this instance.
     */
    public function __construct(
        public int $timeout = 5,
    ) {
    }

    /**
     * Tried and true.
     *
     * @throws FetchFailedException
     * @throws DataConversionException
     */
    public function fetchIndexLite(Player $player, bool $oldSchool): OsrsHighScore|Rs3HighScore
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
        foreach($data->activities as $activity) {
            $time = new \DateTime($activity->date);
            $time->setTimezone(new \DateTimeZone('UTC'));

            $activities[] = new ActivityFeedItem(
                $time,
                trim($activity->text),
                trim($activity->details)
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
            $time = new \DateTime($itemElement->pubDate);
            $time->setTimezone(new \DateTimeZone('UTC'));
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
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout
        ]);
        $data = (string)curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new FetchFailedException(sprintf("A cURL error occurred for \"%s\": %s", $url, $error));
        }

        if ($statusCode !== 200) {
            throw new FetchFailedException(sprintf("URL \"%s\" responded with status code %d.", $url, $statusCode));
        }

        if (!$data) {
            throw new FetchFailedException(sprintf("URL \"%s\" returned no data.", $url));
        }

        return $data;
    }
}
