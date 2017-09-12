<?php

namespace Villermen\RuneScape;

use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\HighScore\HighScore;

class Player
{
    const HIGH_SCORE_URL = "http://services.runescape.com/m=hiscore/index_lite.ws?player=%s";
    const HIGH_SCORE_URL_OLD_SCHOOL = "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s";
    const ADVENTURERS_LOG_URL = "https://apps.runescape.com/runemetrics/app/overview/player/%s";
    const ACTIVITY_FEED_URL = "http://services.runescape.com/m=adventurers-log/a=13/rssfeed?searchName=%s";
    const CHAT_HEAD_URL = "https://secure.runescape.com/m=avatar-rs/%s/chat.gif";
    const FULL_BODY_URL = "https://secure.runescape.com/m=avatar-rs/%s/full.gif";

    /** @var HighScore */
    private $cachedHighScoreData;

    /** @var string */
    protected $name;

    /**
     * Returns whether the given name is a valid RuneScape player name.
     *
     * @param string $name
     * @return bool
     */
    public static function validateName(string $name): bool
    {
        return preg_match("/^[a-z0-9 -_]{1,12}$/i", $name);
    }

    public function __construct(string $name)
    {
        $name = trim($name);

        // Validate that name adheres to RS policies
        if (!self::validateName($name)) {
            throw new RuneScapeException("Name does not conform to the RuneScape specifications.");
        }

        $this->name = $name;
    }

    /**
     * Returns the live high score of the player from the official high scores (expect a delay).
     * Subsequent calls will not cause a request.
     *
     * @param bool $oldSchool If true, oldschool stats will be queried.
     * @param int $timeOut Timeout of the high score request in seconds.
     * @return HighScore
     * @throws RuneScapeException
     */
    public function getHighScore(bool $oldSchool = false, int $timeOut = 5): HighScore
    {
        return new HighScore($this, $this->getHighScoreData($oldSchool, $timeOut));
    }

    protected function getHighScoreData(bool $oldSchool = false, int $timeOut = 5): string
    {
        if ($this->cachedHighScoreData) {
            return $this->cachedHighScoreData;
        }

        $requestUrl = sprintf($oldSchool ? self::HIGH_SCORE_URL_OLD_SCHOOL : self::HIGH_SCORE_URL, urlencode($this->getName()));

        $context = stream_context_create([
            "http" => [
                "timeout" => $timeOut,
            ]
        ]);

        $data = @file_get_contents($requestUrl, false, $context);

        if (!$data) {
            throw new RuneScapeException("Could not obtain player stats from RuneScape high scores.");
        }

        return $this->cachedHighScoreData = $data;
    }

    /**
     * Returns the activities currently displayed on the player's activity feed.
     *
     * @param int $timeOut
     * @return ActivityFeed
     */
    public function getActivityFeed(int $timeOut = 5): ActivityFeed
    {
        return new ActivityFeed($this->getActivityFeedData($timeOut));
    }

    /**
     * Returns the raw activity feed data.
     *
     * @param int $timeOut
     * @return string
     * @throws RuneScapeException
     */
    protected function getActivityFeedData(int $timeOut = 5): string
    {
        $curl = curl_init($this->getActivityFeedUrl());
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeOut
        ]);
        $feedData = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode !== 200) {
            throw new RuneScapeException("Activity feed page returned with status " . $statusCode);
        }

        return $feedData;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getChatHeadUrl(): string
    {
        return sprintf(self::CHAT_HEAD_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getFullBodyUrl(): string
    {
        return sprintf(self::FULL_BODY_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getAdventurersLogUrl(): string
    {
        return sprintf(self::ADVENTURERS_LOG_URL, $this->getName());
    }

    /**
     * @return string
     */
    public function getActivityFeedUrl(): string
    {
        return sprintf(self::ACTIVITY_FEED_URL, $this->getName());
    }
}
