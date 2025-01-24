<?php

namespace Villermen\RuneScape\Service;

use Symfony\Component\DomCrawler\Crawler;
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
use Villermen\RuneScape\PlayerData\GroupIronmanData;
use Villermen\RuneScape\PlayerData\RuneMetricsData;

/**
 * Fetches and converts external API data to usable objects.
 */
class PlayerDataFetcher
{
    protected readonly HttpClientInterface $httpClient;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? HttpClient::create([
            'timeout' => 10,
        ]);
    }

    /**
     * The tried and true way to obtain high score data.
     *
     * @throws FetchFailedException
     * @throws DataConversionException
     */
    public function fetchIndexLite(Player $player, bool $oldSchool): OsrsHighScore|Rs3HighScore
    {
        $url = $oldSchool
            ? 'https://secure.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s'
            : 'https://secure.runescape.com/m=hiscore/index_lite.ws?player=%s';
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
                    'id' => count($skills),
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
                    'id' => count($activities),
                    'rank' => HighScore::correctValue($rank),
                    'score' => HighScore::correctValue($score),
                ];
                continue;
            }

            throw new DataConversionException(
                sprintf('Invalid high score data with size %s supplied.', count($entryArray))
            );
        }

        return HighScore::fromArray([
            'skills' => $skills,
            'activities' => $activities,
        ], $oldSchool);
    }

    /**
     * May yield unranked and non-member RS3 high score data, but lacks high score activities.
     *
     * @throws FetchFailedException
     * @throws DataConversionException
     */
    public function fetchRuneMetrics(Player $player): RuneMetricsData
    {
        $url = sprintf('https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20', urlencode($player->getName()));
        $data = @json_decode($this->fetchUrl($url), associative: true);

        if (!$data) {
            throw new DataConversionException('Could not decode RuneMetrics API response.');
        }

        if (isset($data['error'])) {
            throw new FetchFailedException(
                'RuneMetrics API returned an error. User may not exist or have their profile set to private.'
            );
        }

        // HighScore
        $totalRank = isset($data['rank']) ? (int)str_replace(',', '', $data['rank']) : null;

        $skills = [
            [
                'id' => 0,
                'rank' => $totalRank,
                'level' => $data['totalskill'],
                'xp' => $data['totalxp'],
            ]
        ];

        foreach($data['skillvalues'] as $skillvalue) {
            $skills[] = [
                // +1 because total is not considered a skill.
                'id' => $skillvalue['id'] + 1,
                'rank' =>  $skillvalue['rank'] ?? null,
                'level' => $skillvalue['level'],
                'xp' => (int)($skillvalue['xp'] / 10),
            ];
        }

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
            $data['name'],
            HighScore::fromArray([
                'skills' => $skills,
                'activities' => [],
            ], oldSchool: false),
            new ActivityFeed($activities),
        );
    }

    /**
     * @throws DataConversionException
     * @throws FetchFailedException
     */
    public function fetchAdventurersLog(Player $player): AdventurersLogData
    {
        $url = sprintf(
            'https://secure.runescape.com/m=adventurers-log/rssfeed?searchName=%s',
            urlencode($player->getName())
        );
        $data = $this->fetchUrl($url);

        // Parse data into ActivityFeed object
        $crawler = new Crawler($data);
        $itemCrawler = $crawler->filter('rss > channel > item');
        if ($itemCrawler->count() === 0) {
            throw new DataConversionException('Could not obtain any feed items from feed.');
        }

        $feedItems = $itemCrawler->each(function (Crawler $subCrawler) {
            $time = new \DateTimeImmutable($subCrawler->children('pubDate')->innerText(), new \DateTimeZone('UTC'));
            $title = trim($subCrawler->children('title')->innerText());
            $description = trim($subCrawler->children('description')->innerText());

            if (!$title || !$description) {
                throw new DataConversionException(sprintf(
                    'Could not parse one of the activity feed items. (time: %s, title: %s, description: %s)',
                    $time->format('j-n-Y'), $title, $description
                ));
            }

            return new ActivityFeedItem($time, $title, $description);
        });

        // Parse real name
        $titleCrawler = $crawler->filter('rss > channel > title');
        if ($titleCrawler->count() === 0) {
            throw new DataConversionException('Could not obtain player name element from feed.');
        }

        $title = $titleCrawler->innerText();
        $displayName = trim(substr($title, strrpos($title, ':') + 1));

        return new AdventurersLogData(
            $displayName,
            new ActivityFeed($feedItems),
        );
    }

    /**
     * @throws DataConversionException
     * @throws FetchFailedException
     */
    public function fetchGroupIronman(string $groupName, bool $oldSchool): GroupIronmanData
    {
        if (!$oldSchool) {
            // What horrendous interface they have...
            // Probably:
            // 1. https://secure.runescape.com/m=runescape_gim_hiscores//v1/groupScores/find/byGroupName/best%20bwanas?size=1&isCompetitive=false (+true)
            // 2. Use size in https://rs.runescape.com/hiscores/group-ironman/regular/2-player/gim%20twins
            // 3. All classes are garbage, use structure: section > h1 (group name) + div > (div figure + a (playername))
            throw new \InvalidArgumentException('RS3 ironman lookup is not yet supported.');
        }

        $urls = [
            sprintf(
                'https://secure.runescape.com/m=hiscore_oldschool_ironman/group-ironman/view-group?name=%s',
                urlencode($groupName)
            ),
            sprintf(
                'https://secure.runescape.com/m=hiscore_oldschool_hardcore_ironman/group-ironman/view-group?name=%s',
                urlencode($groupName)
            ),
        ];

        foreach ($urls as $url) {
            try {
                $data = $this->fetchUrl($url);

                $crawler = new Crawler($data);

                $displayName = $this->normalizeName($crawler->filter('.uc-scroll__group-title')->innerText());

                $players = $crawler
                    ->filter('.uc-scroll__table-row--type-player .uc-scroll__link')
                    ->each(fn (Crawler $subCrawler) => new Player($this->normalizeName($subCrawler->innerText())));

                if (count($players) === 0) {
                    throw new DataConversionException(sprintf('Ironman group "%s" seems to have no members.', $groupName));
                }

                return new GroupIronmanData($displayName, $players);
            } catch (FetchFailedException $lastException) {
            }
        }

        throw new FetchFailedException(
            sprintf('Failed to obtain group ironman data for "%s" on both regular and hardore leaderboards.', $groupName),
            previous: $lastException,
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
            $response = $this->httpClient->request('GET', $url , [
                'max_redirects' => 0,
            ]);
            $data = $response->getContent();
            if (!$data) {
                throw new FetchFailedException(sprintf('URL "%s" returned no data.', $url));
            }

            return $data;
        } catch (ExceptionInterface $exception) {
            throw new FetchFailedException(sprintf('An exception occurred while fetching "%s": %s', $url, $exception->getMessage()), previous: $exception);
        }
    }

    protected function normalizeName(string $name): string
    {
        return trim(str_replace("\u{A0}", ' ', $name));
    }
}
