<?php

namespace History\Items;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat as A;

class LegendarySword extends Custom {

    const CUSTOM_ITEM = "CustomItem";

    /**
     * LegendarySword Constructor.
     */
    public function __construct() {
        parent::__construct(self::DIAMOND_SWORD, A::RED."LegendarySword", [A::BOLD.A::DARK_PURPLE."Special".A::RESET."\n\n".
        A::GRAY."La gran espada legendaria\n\n".
        A::GOLD."Por los Dioses"]);
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM_ITEM));
    }

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return 1;
    }
}