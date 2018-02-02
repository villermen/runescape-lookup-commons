<?php

namespace Villermen\RuneScape;

use DateTime;
use Exception;
use SimpleXMLElement;
use Villermen\RuneScape\ActivityFeed\ActivityFeed;
use Villermen\RuneScape\ActivityFeed\ActivityFeedItem;
use Villermen\RuneScape\Exception\FetchFailedException;
use Villermen\RuneScape\Exception\RuneScapeException;
use Villermen\RuneScape\HighScore\ActivityHighScore;
use Villermen\RuneScape\HighScore\SkillHighScore;
use Villermen\RuneScape\HighScore\HighScoreActivity;
use Villermen\RuneScape\HighScore\HighScoreSkill;

class PlayerDataFetcher
{
    const HIGH_SCORE_URL = "http://services.runescape.com/m=hiscore/index_lite.ws?player=%s";
    const OLD_SCHOOL_HIGH_SCORE_URL = "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=%s";
    const ADVENTURERS_LOG_URL = "http://services.runescape.com/m=adventurers-log/a=13/rssfeed?searchName=%s";
    const RUNEMETRICS_URL = "https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20";

    const OLD_SCHOOL_ACTIVITY_MAPPING = [Activity::ACTIVITY_EASY_CLUE_SCROLLS, Activity::ACTIVITY_MEDIUM_CLUE_SCROLLS, Activity::ACTIVITY_BOUNTY_HUNTER_ROGUES, Activity::ACTIVITY_BOUNTY_HUNTER, Activity::ACTIVITY_HARD_CLUE_SCROLLS, Activity::ACTIVITY_LAST_MAN_STANDING, Activity::ACTIVITY_ELITE_CLUE_SCROLLS, Activity::ACTIVITY_MASTER_CLUE_SCROLLS];

    /** @var SkillHighScore[] */
    protected $cachedSkillHighScores = [];

    /** @var SkillHighScore[] */
    protected $cachedOldSchoolSkillHighScores = [];

    /** @var ActivityHighScore[] */
    protected $cachedActivityHighScores = [];

    /** @var ActivityHighScore[] */
    protected $cachedOldSchoolActivityHighScores = [];

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
        if (isset($this->cachedRealNames[$this->getCacheKey($player)])) {
            return $this->cachedRealNames[$this->getCacheKey($player)];
        }

        try {
            $this->fetchRuneMetrics($player);
        } catch (FetchFailedException $exception1) {
            $this->fetchAdventurersLog($player);
        }

        return $this->cachedRealNames[$this->getCacheKey($player)];
    }

    /**
     * @param Player $player
     * @return SkillHighScore
     * @throws FetchFailedException
     */
    public function fetchSkillHighScore(Player $player): SkillHighScore
    {
        if (isset($this->cachedSkillHighScores[$this->getCacheKey($player)])) {
            return $this->cachedSkillHighScores[$this->getCacheKey($player)];
        }

        try {
            $this->fetchRuneMetrics($player);
        } catch (FetchFailedException $exception1) {
            $this->fetchIndexLite($player, false);
        }

        return $this->cachedSkillHighScores[$this->getCacheKey($player)];
    }

    /**
     * @param Player $player
     * @return SkillHighScore
     * @throws FetchFailedException
     */
    public function fetchOldSchoolSkillHighScore(Player $player): SkillHighScore
    {
        if (isset($this->cachedOldSchoolSkillHighScores[$this->getCacheKey($player)])) {
            return $this->cachedOldSchoolSkillHighScores[$this->getCacheKey($player)];
        }

        $this->fetchIndexLite($player, true);

        return $this->cachedOldSchoolSkillHighScores[$this->getCacheKey($player)];
    }

    /**
     * @param Player $player
     * @return ActivityHighScore
     * @throws FetchFailedException
     */
    public function fetchActivityHighScore(Player $player): ActivityHighScore
    {
        if (isset($this->cachedActivityHighScores[$this->getCacheKey($player)])) {
            return $this->cachedActivityHighScores[$this->getCacheKey($player)];
        }

        $this->fetchIndexLite($player, false);

        return $this->cachedActivityHighScores[$this->getCacheKey($player)];
    }

    /**
     * @param Player $player
     * @return ActivityHighScore
     * @throws FetchFailedException
     */
    public function fetchOldSchoolActivityHighScore(Player $player): ActivityHighScore
    {
        if (isset($this->cachedOldSchoolActivityHighScores[$this->getCacheKey($player)])) {
            return $this->cachedOldSchoolActivityHighScores[$this->getCacheKey($player)];
        }

        $this->fetchIndexLite($player, true);


        return $this->cachedOldSchoolActivityHighScores[$this->getCacheKey($player)];
    }

    /**
     * @param Player $player
     * @return ActivityFeed
     * @throws FetchFailedException
     */
    public function fetchActivityFeed(Player $player): ActivityFeed
    {

        if (isset($this->cachedActivityFeeds[$this->getCacheKey($player)])) {
            return $this->cachedActivityFeeds[$this->getCacheKey($player)];
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

        return $this->cachedActivityFeeds[$this->getCacheKey($player)];
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
     * @return string
     */
    protected function getCacheKey(Player $player)
    {
        return strtolower($player->getName());
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Caches realName, skillHighScore and activityFeed.
     *
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
        $data = @file_get_contents(sprintf(self::RUNEMETRICS_URL, urlencode($player->getName())), false, $context);

        if (!$data) {
            throw new FetchFailedException("No response from RuneMetrics API.");
        }

        $data = @json_decode($data);

        if (!$data) {
            throw new FetchFailedException("Could not decode RuneMetrics API response.");
        }

        if (isset($data->error)) {
            throw new FetchFailedException("RuneMetrics API returned an error. User might not exist.");
        }

        // Cache SkillHighScore
        $skills = [];
        foreach($data->skillvalues as $skillvalue) {
            $skillId = $skillvalue->id + 1;

            try {
                $skill = Skill::getSkill($skillId);

                $skills[] = new HighScoreSkill($skill, $skillvalue->rank, $skillvalue->level, $skillvalue->xp);
            } catch (RuneScapeException $exception) {
            }
        }

        // Add total
        /** @noinspection PhpUnhandledExceptionInspection */
        $skills[] = new HighScoreSkill(
            Skill::getSkill(Skill::SKILL_TOTAL),
            str_replace(",", "", $data->rank),
            $data->totalskill, $data->totalxp
        );

        $this->cachedSkillHighScores[$this->getCacheKey($player)] = new SkillHighScore($skills, false);

        // Cache ActivityFeed
        $activities = [];
        foreach($data->activities as $activity) {
            $activities[] = new ActivityFeedItem(new DateTime($activity->date), $activity->text, $activity->details);
        }

        $this->cachedActivityFeeds[$this->getCacheKey($player)] = new ActivityFeed($activities);

        // Cache realName
        $this->cachedRealNames[$this->getCacheKey($player)] = $data->name;
    }

    /**
     * Caches skillHighScore and activityHighScore.
     *
     * @param Player $player
     * @param bool $oldSchool
     * @throws FetchFailedException
     */
    protected function fetchIndexLite(Player $player, bool $oldSchool)
    {
        // Fetch data
        $requestUrl = sprintf($oldSchool ? self::OLD_SCHOOL_HIGH_SCORE_URL : self::HIGH_SCORE_URL, urlencode($player->getName()));
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
                    $skills[] = new HighScoreSkill($skill, $rank, $level, $xp);
                } catch (RuneScapeException $exception) {
                }

                $skillId++;
            } elseif (count($entryArray) == 2) {
                // Activity
                try {
                    if ($oldSchool) {
                        if (isset(self::OLD_SCHOOL_ACTIVITY_MAPPING[$activityId])) {
                            $mappedActivityId = self::OLD_SCHOOL_ACTIVITY_MAPPING[$activityId];
                        } else {
                            throw new RuneScapeException("Non-existent old school activity.");
                        }
                    } else {
                        $mappedActivityId = $activityId;
                    }

                    $activity = Activity::getActivity($mappedActivityId);
                    list($rank, $score) = $entryArray;

                    $activities[] = new HighScoreActivity($activity, $rank, $score);
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

        $highScore = new SkillHighScore($skills, $oldSchool);

        if ($oldSchool) {
            $this->cachedOldSchoolSkillHighScores[$this->getCacheKey($player)] = $highScore;
        } else {
            $this->cachedSkillHighScores[$this->getCacheKey($player)] = $highScore;
        }
    }

    /**
     * Caches activityFeed.
     *
     * @param Player $player
     * @throws FetchFailedException
     */
    protected function fetchAdventurersLog(Player $player)
    {
        // Fetch data
        $curl = curl_init(sprintf(self::ADVENTURERS_LOG_URL, urlencode($player->getName())));
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
            $time = new DateTime($itemElement->pubDate);
            $title = trim((string)$itemElement->title);
            $description = trim((string)$itemElement->description);

            if (!$time || !$title || !$description) {
                throw new FetchFailedException(sprintf(
                    "Could not parse one of the activity feed items. (time: %s, title: %s, description: %s)",
                    $time ? $time->format("j-n-Y") : "", $title, $description
                ));
            }

            $feedItems[] = new ActivityFeedItem($time, $title, $description);
        }

        $this->cachedActivityFeeds[$this->getCacheKey($player)] = new ActivityFeed($feedItems);

        // TODO: Parse real name (rss/channel/title)
    }
}
