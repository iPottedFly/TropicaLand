<?php

namespace History\Items;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat as A;

class PlumaMagica extends Custom {

    const CUSTOM_ITEM = "CustomItem";

    /**
     *  PlumaMagica Constructor.
     */
    public function __construct() {
        parent::__construct(self::FEATHER, A::BOLD.A::GOLD."PlumaMagica", [A::BOLD.A::DARK_PURPLE."Special".A::RESET."\n\n".
        A::GRAY."Recibe Poderes Magicos"]);
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM_ITEM));
    }

    public function getMaxStackSize(): int {
        return 1;
    }
}