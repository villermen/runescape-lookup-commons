<?php

namespace Villermen\RuneScape\ActivityFeed;

class ActivityFeed
{
    /** @var list<ActivityFeedItem> */
    public readonly array $items;

    /**
     * @param ActivityFeedItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    /**
     * Returns all feed items in this feed that occur after the given item.
     *
     * @return ActivityFeedItem[]
     */
    public function getItemsAfter(ActivityFeedItem $targetItem): array
    {
        $newerItems = [];

        foreach($this->items as $item) {
            if ($item->equals($targetItem)) {
                break;
            }

            $newerItems[] = $item;
        }

        return $newerItems;
    }

    /**
     * Merges this ActivityFeed with a newer feed.
     * Returns a new feed with all new items from the newerFeed prepended to it.
     */
    public function merge(ActivityFeed $newerFeed): ActivityFeed
    {
        if (count($this->items) > 0) {
            $prepend = $newerFeed->getItemsAfter($this->items[0]);
        } else {
            $prepend = $newerFeed->items;
        }

        return new ActivityFeed(array_merge($prepend, $this->items));
    }
}
