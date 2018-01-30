# RuneScape lookup commons

#### A PHP library containing RuneScape lookup components.

[![CircleCI](https://circleci.com/gh/villermen/runescape-lookup-commons.svg?style=svg)](https://circleci.com/gh/villermen/runescape-lookup-commons)

## Features

Contains properly structured classes for looking up:
- Player high scores (including old school).
- Player activity feeds.

## Usage

For looking up player related matters, simply construct a `Player` object and use its methods:

```php
use Villermen\RuneScape\Player;
use Villermen\RuneScape\Skill;

$player = new Player("Villermen");
$currentOldSchoolHighScore = $player->getHighScore(true);
$uncappedAttackLevel = $currentOldSchoolHighScore->getSkill(Skill::SKILL_ATTACK)->getLevel(true);
```

## Installation

`composer require villermen/runescape-lookup-commons`
