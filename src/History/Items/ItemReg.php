<?php

namespace History\Items;

use pocketmine\item\{Item, ItemFactory};

class ItemReg {

    /**
     * @return void
     */
    public static function init(): void {
        ItemFactory::registerItem(new LegendarySword(), true);
        ItemFactory::registerItem(new PlumaMagica(), true);
    }
}
