<?php

namespace History\Items;

use pocketmine\item\{Item, ProjectileItem};
use pocketmine\Player;
use pocketmine\math\Vector3;

abstract class Custom extends Item {

    /**
     * Custom constructor.
     * @param Int|null $id
     * @param String|null $name
     * @param array|null $lore
     * @param array|null $enchantments
     * @param Int|null $meta
     */
    public function __construct(?Int $id, ?String $name, ?array $lore = [], ?array $enchantments = [], ?Int $meta = 0) {
        $this->setCustomName($name);
        $this->setLore($lore);
        if(!empty($enchantments)) {
            foreach($enchantments as $enchant) {
                $this->addEnchantment($enchant);
            }
        }
        parent::__construct($id, $meta);
    }
}