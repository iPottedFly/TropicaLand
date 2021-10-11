<?php


namespace History\Items\admin;

use History\Items\Custom;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat as A;

class Vanish extends Custom {

    const CUSTOM_ITEM = "CustomItem";

    /**
     * Vanish Constructor.
     */
    public function __construct() {
        parent::__construct("106", A::BOLD.A::GREEN."Vanish", [A::BOLD.A::DARK_PURPLE."Special".A::RESET."\n\n".
        A::GRAY."Use vanish for hide you"]);
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM_ITEM));
    }

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return 1;
    }
}