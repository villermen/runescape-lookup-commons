<?php

namespace Villermen\RuneScape;

class Skill
{
    const SKILL_TOTAL = 0;
    const SKILL_ATTACK = 1;
    const SKILL_DEFENCE = 2;
    const SKILL_STRENGTH = 3;
    const SKILL_CONSTITUTION = 4;
    const SKILL_RANGED = 5;
    const SKILL_PRAYER = 6;
    const SKILL_MAGIC = 7;
    const SKILL_COOKING = 8;
    const SKILL_WOODCUTTING = 9;
    const SKILL_FLETCHING = 10;
    const SKILL_FISHING = 11;
    const SKILL_FIREMAKING = 12;
    const SKILL_CRAFTING = 13;
    const SKILL_SMITHING = 14;
    const SKILL_MINING = 15;
    const SKILL_HERBLORE = 16;
    const SKILL_AGILITY = 17;
    const SKILL_THIEVING = 18;
    const SKILL_SLAYER = 19;
    const SKILL_FARMING = 20;
    const SKILL_RUNECRAFTING = 21;
    const SKILL_HUNTER = 22;
    const SKILL_CONSTRUCTION = 23;
    const SKILL_SUMMONING = 24;
    const SKILL_DUNGEONEERING = 25;
    const SKILL_DIVINATION = 26;
    const SKILL_INVENTION = 27;

    /** @var int[] */
    const XP_TABLE = [1 => 0, 2 => 83, 3 => 174, 4 => 276, 5 => 388, 6 => 512, 7 => 650, 8 => 801, 9 => 969, 10 => 1154, 11 => 1358, 12 => 1584, 13 => 1833, 14 => 2107, 15 => 2411, 16 => 2746, 17 => 3115, 18 => 3523, 19 => 3973, 20 => 4470, 21 => 5018, 22 => 5624, 23 => 6291, 24 => 7028, 25 => 7842, 26 => 8740, 27 => 9730, 28 => 10824, 29 => 12031, 30 => 13363, 31 => 14833, 32 => 16456, 33 => 18247, 34 => 20224, 35 => 22406, 36 => 24815, 37 => 27473, 38 => 30408, 39 => 33648, 40 => 37224, 41 => 41171, 42 => 45529, 43 => 50339, 44 => 55649, 45 => 61512, 46 => 67983, 47 => 75127, 48 => 83014, 49 => 91721, 50 => 101333, 51 => 111945, 52 => 123660, 53 => 136594, 54 => 150872, 55 => 166636, 56 => 184040, 57 => 203254, 58 => 224466, 59 => 247886, 60 => 273742, 61 => 302288, 62 => 333804, 63 => 368599, 64 => 407015, 65 => 449428, 66 => 496254, 67 => 547953, 68 => 605032, 69 => 668051, 70 => 737627, 71 => 814445, 72 => 899257, 73 => 992895, 74 => 1096278, 75 => 1210421, 76 => 1336443, 77 => 1475581, 78 => 1629200, 79 => 1798808, 80 => 1986068, 81 => 2192818, 82 => 2421087, 83 => 2673114, 84 => 2951373, 85 => 3258594, 86 => 3597792, 87 => 3972294, 88 => 4385776, 89 => 4842295, 90 => 5346332, 91 => 5902831, 92 => 6517253, 93 => 7195629, 94 => 7944614, 95 => 8771558, 96 => 9684577, 97 => 10692629, 98 => 11805606, 99 => 13034431, 100 => 14391160, 101 => 15889109, 102 => 17542976, 103 => 19368992, 104 => 21385073, 105 => 23611006, 106 => 26068632, 107 => 28782069, 108 => 31777943, 109 => 35085654, 110 => 38737661, 111 => 42769801, 112 => 47221641, 113 => 52136869, 114 => 57563718, 115 => 63555443, 116 => 70170840, 117 => 77474828, 118 => 85539082, 119 => 94442737, 120 => 104273167, 121 => 115126838, 122 => 127110260, 123 => 140341028, 124 => 154948977, 125 => 171077457, 126 => 188884740];

    /** @var int[] */
    const ELITE_XP_TABLE = [1 => 0, 2 => 830, 3 => 1861, 4 => 2902, 5 => 3980, 6 => 5126, 7 => 6390, 8 => 7787, 9 => 9400, 10 => 11275, 11 => 13605, 12 => 16372, 13 => 19656, 14 => 23546, 15 => 28138, 16 => 33520, 17 => 39809, 18 => 47109, 19 => 55535, 20 => 64802, 21 => 77190, 22 => 90811, 23 => 106221, 24 => 123573, 25 => 143025, 26 => 164742, 27 => 188893, 28 => 215651, 29 => 245196, 30 => 277713, 31 => 316311, 32 => 358547, 33 => 404634, 34 => 454796, 35 => 509259, 36 => 568254, 37 => 632019, 38 => 700797, 39 => 774834, 40 => 854383, 41 => 946227, 42 => 1044569, 43 => 1149696, 44 => 1261903, 45 => 1381488, 46 => 1508756, 47 => 1644015, 48 => 1787581, 49 => 1939773, 50 => 2100917, 51 => 2283490, 52 => 2476369, 53 => 2679907, 54 => 2894505, 55 => 3120508, 56 => 3358307, 57 => 3608290, 58 => 3870846, 59 => 4146374, 60 => 4435275, 61 => 4758122, 62 => 5096111, 63 => 5449685, 64 => 5819299, 65 => 6205407, 66 => 6608473, 67 => 7028964, 68 => 7467354, 69 => 7924122, 70 => 8399751, 71 => 8925664, 72 => 9472665, 73 => 10041285, 74 => 10632061, 75 => 11245538, 76 => 11882262, 77 => 12542789, 78 => 13227679, 79 => 13937496, 80 => 14672812, 81 => 15478994, 82 => 16313404, 83 => 17176661, 84 => 18069395, 85 => 18992239, 86 => 19945833, 87 => 20930821, 88 => 21947856, 89 => 22997593, 90 => 24080695, 91 => 25259906, 92 => 26475754, 93 => 27728955, 94 => 29020233, 95 => 30350318, 96 => 31719944, 97 => 33129852, 98 => 34580790, 99 => 36073511, 100 => 37608773, 101 => 39270442, 102 => 40978509, 103 => 42733789, 104 => 44537107, 105 => 46389292, 106 => 48291180, 107 => 50243611, 108 => 52247435, 109 => 54303504, 110 => 56412678, 111 => 58575823, 112 => 60793812, 113 => 63067521, 114 => 65397835, 115 => 67785643, 116 => 70231841, 117 => 72737330, 118 => 75303019, 119 => 77929820, 120 => 80618654, 121 => 83370445, 122 => 86186124, 123 => 89066630, 124 => 92012904, 125 => 95025896, 126 => 98106559, 127 => 101255855, 128 => 104474750, 129 => 107764216, 130 => 111125230, 131 => 114558777, 132 => 118065845, 133 => 121647430, 134 => 125304532, 135 => 129038159, 136 => 132849323, 137 => 136739041, 138 => 140708338, 139 => 144758242, 140 => 148889790, 141 => 153104021, 142 => 157401983, 143 => 161784728, 144 => 166253312, 145 => 170808801, 146 => 175452262, 147 => 180184770, 148 => 185007406, 149 => 189921255, 150 => 194927409];

    /** @var Skill[]|null */
    private static $skills;

    /**
     * Initializes the skills array.
     * Default values don't allow expressions (new objects).
     */
    public static function initializeSkills()
    {
        if (self::$skills) {
            return;
        }

        self::$skills = [
            self::SKILL_TOTAL => new Skill(self::SKILL_TOTAL, "Total"),
            self::SKILL_ATTACK => new Skill(self::SKILL_ATTACK, "Attack"),
            self::SKILL_DEFENCE => new Skill(self::SKILL_DEFENCE, "Defence"),
            self::SKILL_STRENGTH => new Skill(self::SKILL_STRENGTH, "Strength"),
            self::SKILL_CONSTITUTION => new Skill(self::SKILL_CONSTITUTION, "Constitution", false, false, 10),
            self::SKILL_RANGED => new Skill(self::SKILL_RANGED, "Ranged"),
            self::SKILL_PRAYER => new Skill(self::SKILL_PRAYER, "Prayer"),
            self::SKILL_MAGIC => new Skill(self::SKILL_MAGIC, "Magic"),
            self::SKILL_COOKING => new Skill(self::SKILL_COOKING, "Cooking"),
            self::SKILL_WOODCUTTING => new Skill(self::SKILL_WOODCUTTING, "Woodcutting"),
            self::SKILL_FLETCHING => new Skill(self::SKILL_FLETCHING, "Fletching"),
            self::SKILL_FISHING => new Skill(self::SKILL_FISHING, "Fishing"),
            self::SKILL_FIREMAKING => new Skill(self::SKILL_FIREMAKING, "Firemaking"),
            self::SKILL_CRAFTING => new Skill(self::SKILL_CRAFTING, "Crafting"),
            self::SKILL_SMITHING => new Skill(self::SKILL_SMITHING, "Smithing"),
            self::SKILL_MINING => new Skill(self::SKILL_MINING, "Mining"),
            self::SKILL_HERBLORE => new Skill(self::SKILL_HERBLORE, "Herblore"),
            self::SKILL_AGILITY => new Skill(self::SKILL_AGILITY, "Agility"),
            self::SKILL_THIEVING => new Skill(self::SKILL_THIEVING, "Thieving"),
            self::SKILL_SLAYER => new Skill(self::SKILL_SLAYER, "Slayer", true),
            self::SKILL_FARMING => new Skill(self::SKILL_FARMING, "Farming"),
            self::SKILL_RUNECRAFTING => new Skill(self::SKILL_RUNECRAFTING, "Runecrafting"),
            self::SKILL_HUNTER => new Skill(self::SKILL_HUNTER, "Hunter"),
            self::SKILL_CONSTRUCTION => new Skill(self::SKILL_CONSTRUCTION, "Construction"),
            self::SKILL_SUMMONING => new Skill(self::SKILL_SUMMONING, "Summoning"),
            self::SKILL_DUNGEONEERING => new Skill(self::SKILL_DUNGEONEERING, "Dungeoneering", true),
            self::SKILL_DIVINATION => new Skill(self::SKILL_DIVINATION, "Divination"),
            self::SKILL_INVENTION => new Skill(self::SKILL_INVENTION, "Invention", true, true)
        ];
    }

    /**
     * @return Skill[]
     */
    public static function getSkills(): array
    {
        self::initializeSkills();

        return self::$skills;
    }

    /**
     * Retrieve a skill by ID.
     * You can use the SKILL_ constants in this class for IDs.
     *
     * @param int $id
     * @return Skill
     * @throws RuneScapeException When the requested skill does not exist.
     */
    public static function getSkill(int $id): Skill
    {
        self::initializeSkills();

        if (!isset(self::$skills[$id])) {
            throw new RuneScapeException(sprintf("Skill with id %d does not exist.", $id));
        }

        return self::$skills[$id];
    }

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $highLevelCap;

    /** @var bool */
    protected $elite;

    /** @var int */
    protected $minimumLevel;

    /**
     * @param int $id
     * @param string $name
     * @param bool $highLevelCap
     * @param bool $elite
     * @param int $minimumLevel
     */
    public function __construct(int $id, string $name, bool $highLevelCap = false, bool $elite = false, int $minimumLevel = 1)
    {
        $this->id = $id;
        $this->name = $name;
        $this->highLevelCap = $highLevelCap;
        $this->elite = $elite;
        $this->minimumLevel = $minimumLevel;
    }

    /**
     * Returns the level in this skill for the given XP.
     *
     * @param int $xp
     * @param bool $uncapped
     * @return int
     */
    public function getLevel(int $xp, bool $uncapped = false): int
    {
        $xpTable = $this->getXpTable();

        for ($level = 1; $level <= count($xpTable); $level++) {
            if ($xpTable[$level] > $xp) {
                $level--;
                break;
            }
        }

        // Cap
        if (!$uncapped) {
            $levelCap = $this->isHighLevelCap() ? 120 : 99;

            if ($level > $levelCap) {
                $level = $levelCap;
            }
        }

        return $level;
    }

    /**
     * Returns the XP corresponding to the given level of this skill.
     * Returns false if the level is too high or low to be represented.
     *
     * @param int $level
     * @return int|false
     */
    public function getXp(int $level)
    {
        $xpTable = $this->getXpTable();

        if (!isset($xpTable[$level])) {
            return false;
        }

        return $this->getXpTable()[$level];
    }

    /**
     * @return int[]
     */
    public function getXpTable(): array
    {
        return $this->isElite() ? self::ELITE_XP_TABLE : self::XP_TABLE;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isHighLevelCap(): bool
    {
        return $this->highLevelCap;
    }

    /**
     * @return bool
     */
    public function isElite(): bool
    {
        return $this->elite;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMinimumLevel(): int
    {
        return $this->minimumLevel;
    }
}
