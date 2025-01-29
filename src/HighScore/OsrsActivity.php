<?php

namespace Villermen\RuneScape\HighScore;

/**
 * Note: Easy to scrape from https://secure.runescape.com/m=hiscore_oldschool/index_lite.json?player=NAME
 */
enum OsrsActivity: int implements ActivityInterface
{
    case LEAGUE_POINTS = 0;
    case DEADMAN_POINTS = 1;
    case BOUNTY_HUNTER_HUNTER = 2;
    case BOUNTY_HUNTER_ROGUE = 3;
    case BOUNTY_HUNTER_HUNTER_LEGACY = 4;
    case BOUNTY_HUNTER_ROGUE_LEGACY = 5;
    case CLUE_SCROLLS_ALL = 6;
    case BEGINNER_CLUE_SCROLLS_BEGINNER = 7;
    case CLUE_SCROLLS_EASY = 8;
    case CLUE_SCROLLS_MEDIUM = 9;
    case CLUE_SCROLLS_HARD = 10;
    case CLUE_SCROLLS_ELITE = 11;
    case CLUE_SCROLLS_MASTER = 12;
    case LAST_MAN_STANDING_RANK = 13;
    case PVP_ARENA_RANK = 14;
    case SOUL_WARS_ZEAL = 15;
    case GUARDIANS_OF_THE_RIFT = 16;
    case COLOSSEUM_GLORY = 17;
    case ABYSSAL_SIRE = 18;
    case ALCHEMICAL_HYDRA = 19;
    case AMOXLIATL = 20;
    case ARAXXOR = 21;
    case ARTIO = 22;
    case BARROWS_CHESTS = 23;
    case BRYOPHYTA = 24;
    case CALLISTO = 25;
    case CALVARION = 26;
    case CERBERUS = 27;
    case CHAMBERS_OF_XERIC = 28;
    case CHAMBERS_OF_XERIC_CHALLENGE_MODE = 29;
    case CHAOS_ELEMENTAL = 30;
    case CHAOS_FANATIC = 31;
    case COMMANDER_ZILYANA = 32;
    case CORPOREAL_BEAST = 33;
    case CRAZY_ARCHAEOLOGIST = 34;
    case DAGANNOTH_PRIME = 35;
    case DAGANNOTH_REX = 36;
    case DAGANNOTH_SUPREME = 37;
    case DERANGED_ARCHAEOLOGIST = 38;
    case DUKE_SUCELLUS = 39;
    case GENERAL_GRAARDOR = 40;
    case GIANT_MOLE = 41;
    case GROTESQUE_GUARDIANS = 42;
    case HESPORI = 43;
    case KALPHITE_QUEEN = 44;
    case KING_BLACK_DRAGON = 45;
    case KRAKEN = 46;
    case KREEARRA = 47;
    case KRIL_TSUTSAROTH = 48;
    case LUNAR_CHESTS = 49;
    case MIMIC = 50;
    case NEX = 51;
    case NIGHTMARE = 52;
    case PHOSANIS_NIGHTMARE = 53;
    case OBOR = 54;
    case PHANTOM_MUSPAH = 55;
    case SARACHNIS = 56;
    case SCORPIA = 57;
    case SCURRIUS = 58;
    case SKOTIZO = 59;
    case SOL_HEREDIT = 60;
    case SPINDEL = 61;
    case TEMPOROSS = 62;
    case THE_GAUNTLET = 63;
    case THE_CORRUPTED_GAUNTLET = 64;
    case THE_HUEYCOATL = 65;
    case THE_LEVIATHAN = 66;
    case THE_WHISPERER = 67;
    case THEATRE_OF_BLOOD = 68;
    case THEATRE_OF_BLOOD_HARD_MODE = 69;
    case THERMONUCLEAR_SMOKE_DEVIL = 70;
    case TOMBS_OF_AMASCUT = 71;
    case TOMBS_OF_AMASCUT_EXPERT_MODE = 72;
    case TZKAL_ZUK = 73;
    case TZTOK_JAD = 74;
    case VARDORVIS = 75;
    case VENENATIS = 76;
    case VETION = 77;
    case VORKATH = 78;
    case WINTERTODT = 79;
    case ZALCANO = 80;
    case ZULRAH = 81;
    case COLLECTION_LOG = 82;

    /**
     * @var array<array{self, int|null}>
     */
    private const API_MUTATIONS = [
        [self::COLLECTION_LOG, 18], // 2025-01-29
    ];

    /**
     * Returns a mapping from API IDs to consistent enum values. Disregard all activity data if the size of this map
     * differs from the list of activities returned by the API to prevent data corruption!
     *
     * This enum's values no longer correspond to the API's IDs (starting 29-01-2025) as activities can be inserted at
     * arbitrary locations. That's bad for keeping track of activities over time, so we apply all mutations since then
     * to create a stable mapping.
     *
     * @return array<int, self>
     */
    public static function createApiMap(): array
    {
        $map = array_slice(self::cases(), 0, self::COLLECTION_LOG->value);
        foreach (self::API_MUTATIONS as [$activity, $insertId]) {
            // @phpstan-ignore identical.alwaysFalse (I don't want to have to write this when it becomes necessary.)
            if ($insertId === null) {
                $map = array_values(array_filter($map, fn (OsrsActivity $mapActivity): bool => (
                    $mapActivity !== $activity
                )));
                continue;
            }

            array_splice($map, $insertId, 0, [$activity]);
        }

        return $map;
    }

    public function getId(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return match ($this) {
            self::LEAGUE_POINTS => 'League Points',
            self::DEADMAN_POINTS => 'Deadman Points',
            self::BOUNTY_HUNTER_HUNTER => 'Bounty Hunter - Hunter',
            self::BOUNTY_HUNTER_ROGUE => 'Bounty Hunter - Rogue',
            self::BOUNTY_HUNTER_HUNTER_LEGACY => 'Bounty Hunter (Legacy) - Hunter',
            self::BOUNTY_HUNTER_ROGUE_LEGACY => 'Bounty Hunter (Legacy) - Rogue',
            self::CLUE_SCROLLS_ALL => 'Clue Scrolls (all)',
            self::BEGINNER_CLUE_SCROLLS_BEGINNER => 'Clue Scrolls (beginner)',
            self::CLUE_SCROLLS_EASY => 'Clue Scrolls (easy)',
            self::CLUE_SCROLLS_MEDIUM => 'Clue Scrolls (medium)',
            self::CLUE_SCROLLS_HARD => 'Clue Scrolls (hard)',
            self::CLUE_SCROLLS_ELITE => 'Clue Scrolls (elite)',
            self::CLUE_SCROLLS_MASTER => 'Clue Scrolls (master)',
            self::LAST_MAN_STANDING_RANK => 'Last Man Standing Rank',
            self::PVP_ARENA_RANK => 'PvP Arena Rank',
            self::SOUL_WARS_ZEAL => 'Soul Wars Zeal',
            self::GUARDIANS_OF_THE_RIFT => 'Guardians of the Rift',
            self::COLOSSEUM_GLORY => 'Colosseum Glory',
            self::ABYSSAL_SIRE => 'Abyssal Sire',
            self::ALCHEMICAL_HYDRA => 'Alchemical Hydra',
            self::AMOXLIATL => 'Amoxliatl',
            self::ARAXXOR => 'Araxxor',
            self::ARTIO => 'Artio',
            self::BARROWS_CHESTS => 'Barrows Chests',
            self::BRYOPHYTA => 'Bryophyta',
            self::CALLISTO => 'Callisto',
            self::CALVARION => 'Calvar\'ion',
            self::CERBERUS => 'Cerberus',
            self::CHAMBERS_OF_XERIC => 'Chambers of Xeric',
            self::CHAMBERS_OF_XERIC_CHALLENGE_MODE => 'Chambers of Xeric: Challenge Mode',
            self::CHAOS_ELEMENTAL => 'Chaos Elemental',
            self::CHAOS_FANATIC => 'Chaos Fanatic',
            self::COMMANDER_ZILYANA => 'Commander Zilyana',
            self::CORPOREAL_BEAST => 'Corporeal Beast',
            self::CRAZY_ARCHAEOLOGIST => 'Crazy Archaeologist',
            self::DAGANNOTH_PRIME => 'Dagannoth Prime',
            self::DAGANNOTH_REX => 'Dagannoth Rex',
            self::DAGANNOTH_SUPREME => 'Dagannoth Supreme',
            self::DERANGED_ARCHAEOLOGIST => 'Deranged Archaeologist',
            self::DUKE_SUCELLUS => 'Duke Sucellus',
            self::GENERAL_GRAARDOR => 'General Graardor',
            self::GIANT_MOLE => 'Giant Mole',
            self::GROTESQUE_GUARDIANS => 'Grotesque Guardians',
            self::HESPORI => 'Hespori',
            self::KALPHITE_QUEEN => 'Kalphite Queen',
            self::KING_BLACK_DRAGON => 'King Black Dragon',
            self::KRAKEN => 'Kraken',
            self::KREEARRA => 'Kree\'Arra',
            self::KRIL_TSUTSAROTH => 'K\'ril Tsutsaroth',
            self::LUNAR_CHESTS => 'Lunar Chests',
            self::MIMIC => 'Mimic',
            self::NEX => 'Nex',
            self::NIGHTMARE => 'Nightmare',
            self::PHOSANIS_NIGHTMARE => 'Phosani\'s Nightmare',
            self::OBOR => 'Obor',
            self::PHANTOM_MUSPAH => 'Phantom Muspah',
            self::SARACHNIS => 'Sarachnis',
            self::SCORPIA => 'Scorpia',
            self::SCURRIUS => 'Scurrius',
            self::SKOTIZO => 'Skotizo',
            self::SOL_HEREDIT => 'Sol Heredit',
            self::SPINDEL => 'Spindel',
            self::TEMPOROSS => 'Tempoross',
            self::THE_GAUNTLET => 'The Gauntlet',
            self::THE_CORRUPTED_GAUNTLET => 'The Corrupted Gauntlet',
            self::THE_HUEYCOATL => 'The Hueycoatl',
            self::THE_LEVIATHAN => 'The Leviathan',
            self::THE_WHISPERER => 'The Whisperer',
            self::THEATRE_OF_BLOOD => 'Theatre of Blood',
            self::THEATRE_OF_BLOOD_HARD_MODE => 'Theatre of Blood: Hard Mode',
            self::THERMONUCLEAR_SMOKE_DEVIL => 'Thermonuclear Smoke Devil',
            self::TOMBS_OF_AMASCUT => 'Tombs of Amascut',
            self::TOMBS_OF_AMASCUT_EXPERT_MODE => 'Tombs of Amascut: Expert Mode',
            self::TZKAL_ZUK => 'TzKal-Zuk',
            self::TZTOK_JAD => 'TzTok-Jad',
            self::VARDORVIS => 'Vardorvis',
            self::VENENATIS => 'Venenatis',
            self::VETION => 'Vet\'ion',
            self::VORKATH => 'Vorkath',
            self::WINTERTODT => 'Wintertodt',
            self::ZALCANO => 'Zalcano',
            self::ZULRAH => 'Zulrah',
            self::COLLECTION_LOG => 'Collection log',
        };
    }
}
