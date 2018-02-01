<?php

namespace Villermen\RuneScape;

use DateTime;
use Exception;
use SimpleXMLElement;
use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\ActivityFeed\ActivityFeedItem;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\Exception\RuneScapeException;
use Villermen\RuneScape\HighScore\HighScore;
use Villermen\RuneScape\HighScore\HighScoreActivity;
use Villermen\RuneScape\HighScore\HighScoreSkill;

class PlayerDataFetcher
{
    const DEFAULT_HIGH_SCORE_URL = "http://services.runescape.com/m=hiscore/index_lite.ws?player=%s";
    const DEFAULT_OLD_SCHOOL_HIGH_SCORE_URL = "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s";
    const DEFAULT_ADVENTURERS_LOG_URL = "http://services.runescape.com/m=adventurers-log/a=13/rssfeed?searchName=%s";
    const DEFAULT_RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20";

    protected $highScoreUrl = self::DEFAULT_HIGH_SCORE_URL;
    protected $oldSchoolHighScoreUrl = self::DEFAULT_OLD_SCHOOL_HIGH_SCORE_URL;
    protected $adventurersLogUrl = self::DEFAULT_ADVENTURERS_LOG_URL;
    protected $runeMetricsUrl = self::DEFAULT_RUNEMETRICS_URL;

    /** @var HighScore[] */
    protected $cachedHighScores = [];

    /** @var HighScore[] */
    protected $cachedOldSchoolHighScores = [];

    /** @var ActivityFeed[] */
    protected $cachedActivityFeeds = [];

    /** @var string[] */
    protected $cachedRealNames = [];

    /** @var int */
    protected $timeOut;

    public function __construct(int $timeOut = 5)
    {
        $this->timeOut = $timeOut;
    }

    /**
     * @param Player $player
     * @return string
     * @throws FetchFailedException
     */
    public function fetchRealName(Player $player): string
    {
        $cacheName = strtolower($player->getName());

        if (isset($this->cachedRealNames[$cacheName])) {
            return $this->cachedRealNames[$cacheName];
        }

        try {
            $this->fetchRuneMetrics($player);
        } catch (FetchFailedException $exception1) {
            try {
                $this->fetchAdventurersLog($player);
            } catch (FetchFailedException $exception2) {
                throw $exception1;
            }
        }

        return $this->cachedRealNames[$cacheName];
    }

    /**
     * @param Player $player
     * @return HighScore
     * @throws FetchFailedException
     */
    public function fetchHighScore(Player $player): HighScore
    {
        $cacheName = strtolower($player->getName());

        if (isset($this->cachedHighScores[$cacheName])) {
            return $this->cachedHighScores[$cacheName];
        }

        try {
            $this->fetchRuneMetrics($player);
        } catch (FetchFailedException $exception1) {
            try {
                $this->fetchIndexLite($player, false);
            } catch (FetchFailedException $exception2) {
                throw $exception1;
            }
        }

        return $this->cachedHighScores[$cacheName];
    }

    /**
     * Set the timeOut used for every external request.
     *
     * @param int $seconds
     */
    public function setTimeOut(int $seconds)
    {
        $this->timeOut = $seconds;
    }

    /**
     * @param Player $player
     * @return HighScore
     * @throws FetchFailedException
     */
    public function fetchOldSchoolHighScore(Player $player): HighScore
    {
        $cacheName = strtolower($player->getName());

        if (isset($this->cachedOldSchoolHighScores[$cacheName])) {
            return $this->cachedOldSchoolHighScores[$cacheName];
        }

        try {
            $this->fetchIndexLite($player, true);
        } catch (FetchFailedException $exception1) {
            throw $exception1;
        }

        return $this->cachedOldSchoolHighScores[$cacheName];
    }

    /**
     * @param Player $player
     * @return ActivityFeed
     * @throws FetchFailedException
     */
    public function fetchActivityFeed(Player $player): ActivityFeed
    {
        $cacheName = strtolower($player->getName());

        if (isset($this->cachedActivityFeeds[$cacheName])) {
            return $this->cachedActivityFeeds[$cacheName];
        }

        try {
            $this->fetchRuneMetrics($player);
        } catch (FetchFailedException $exception1) {
            try {
                $this->fetchAdventurersLog($player);
            } catch (FetchFailedException $exception2) {
                throw $exception1;
            }
        }

        return $this->cachedActivityFeeds[$cacheName];
    }

    /**
     * @param Player $player
     * @throws FetchFailedException
     */
    protected function fetchRuneMetrics(Player $player) {
        // Fetch data
        $context = stream_context_create([
            "http" => [
                "timeout" => $this->timeOut,
            ]
        ]);
        $data = @file_get_contents(sprintf($this->runeMetricsUrl, urlencode($player->getName())), false, $context);

        if (!$data) {
            throw new FetchFailedException("No response from RuneMetrics API.");
        }

        $data = @json_decode($data);

        if (!$data) {
            throw new FetchFailedException("Could not decode RuneMetrics API response.");
        }

        dump($data);

        if ($data->error) {
            throw new FetchFailedException("RuneMetrics API returned an error. User might not exist.");
        }

        dump($data->skillvalues);
        exit();

        // TODO: This HighScore will not contain activities... How to tackle that discrepancy?

        // Parse data into HighScore
        foreach($data->skillvalues as $skillvalue) {

        }

        // TODO: realname

        // TODO: activity feed


    }

    /**
     * Fetches HighScore from index_lite.ws.
     *
     * @param Player $player
     * @param bool $oldSchool
     * @throws FetchFailedException
     */
    protected function fetchIndexLite(Player $player, bool $oldSchool)
    {
        // Fetch data
        $requestUrl = sprintf($oldSchool ? $this->oldSchoolHighScoreUrl : $this->highScoreUrl, urlencode($player->getName()));
        $context = stream_context_create([
            "http" => [
                "timeout" => $this->timeOut,
            ]
        ]);
        $data = @file_get_contents($requestUrl, false, $context);

        if (!$data) {
            throw new FetchFailedException("Could not obtain data from the RuneScape high scores.");
        }

        // Parse data into HighScore object
        $entries = explode("\n", trim($data));

        $skillId = 0;
        $activityId = 0;
        $skills = [];
        $activities = [];

        foreach($entries as $entry) {
            $entryArray = explode(",", $entry);

            if (count($entryArray) == 3) {
                // Skill
                try {
                    $skill = Skill::getSkill($skillId);
                    list($rank, $level, $xp) = $entryArray;
                    $skills[$skillId] = new HighScoreSkill($skill, $rank, $level, $xp);
                } catch (RuneScapeException $exception) {
                }

                $skillId++;
            } elseif (count($entryArray) == 2) {
                // Activity
                try {
                    $activity = Activity::getActivity($activityId);
                    list($rank, $score) = $entryArray;
                    $activities[$activityId] = new HighScoreActivity($activity, $rank, $score);
                } catch (RuneScapeException $exception) {
                }

                $activityId++;
            } else {
                throw new FetchFailedException("Invalid high score data supplied.");
            }
        }

        if (!count($skills) && !count($activities)) {
            throw new FetchFailedException("No high score obtained from data.");
        }

        $highScore = new HighScore($skills, $activities);

        if ($oldSchool) {
            $this->cachedOldSchoolHighScores[strtolower($player->getName())] = $highScore;
        } else {
            $this->cachedHighScores[strtolower($player->getName())] = $highScore;
        }
    }

    /**
     * @param Player $player
     * @throws FetchFailedException
     */
    protected function fetchAdventurersLog(Player $player)
    {
        // Fetch data
        $curl = curl_init(sprintf($this->adventurersLogUrl, urlencode($player->getName())));
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeOut
        ]);
        $data = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode !== 200) {
            throw new FetchFailedException("Activity feed page returned with status " . $statusCode);
        }

        // Parse data into ActivityFeed object
        $feedItems = [];

        try {
            $feed = new SimpleXmlElement($data);
        } catch (Exception $exception) {
            throw new FetchFailedException("Could not parse the activity feed as XML.");
        }

        $itemElements = @$feed->xpath("//item");

        if ($itemElements === false) {
            throw new FetchFailedException("Could not obtain feed items from feed.");
        }

        foreach ($itemElements as $itemElement) {
            $id = trim((string)$itemElement->guid);
            $id = substr($id, strripos($id, "id=") + 3);
            $time = new DateTime($itemElement->pubDate);
            $title = trim((string)$itemElement->title);
            $description = trim((string)$itemElement->description);

            if (!$id || !$time || !$title || !$description) {
                throw new FetchFailedException(sprintf(
                    "Could not parse one of the activity feed items. (id: %s, time: %s, title: %s, description: %s)",
                    $id, $time ? $time->format("j-n-Y") : "", $title, $description
                ));
            }

            $feedItems[] = new ActivityFeedItem($id, $time, $title, $description);
        }

        $this->cachedActivityFeeds[strtolower($player->getName())] = new ActivityFeed($feedItems);

        // TODO: Parse real name (rss/channel/title)
    }
}
