<?php

namespace Villermen\RuneScape\ActivityFeed;

use DateTime;
use SimpleXMLElement;
use Villermen\RuneScape\Player;
use Villermen\RuneScape\RuneScapeException;

class ActivityFeed
{
    private $player;

    public function __construct(Player $player, string $data)
    {
        $this->player = $player;

        try {
            $feed = new SimpleXmlElement($data);
            $feedItems = $feed->xpath("//item");

            $result = [];

            foreach ($feedItems as $feedItem) {
                $id = trim((string)$feedItem->guid);
                $id = substr($id, strripos($id, "id=") + 3);
                $feedTime = new DateTime($feedItem->pubDate);

                $result[] = new ActivityFeedItem($id, $feedTime, trim((string)$feedItem->title),
                    trim((string)$feedItem->description));
            }

            return $result;
        } catch (\Exception $ex) {
            throw new RuneScapeException("Could not parse player's activity feed.", 0, $ex);
        }
    }
}
