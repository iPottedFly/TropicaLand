<?php

namespace History\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as A;

use History\Items\LegendarySword;
use History\Items\PlumaMagica;

class GiveCmd extends Command {

    public function __construct() {
       parent::__construct("items", "Obtienes los items especiales", "/items");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        $sword = new LegendarySword();
        $pluma = new PlumaMagica();
        if(!$sender instanceof Player) {
            $sender->sendMessage(A::RED."¡ Error !");
            return;
        }
        $sender->getPlayer()->getInventory()->addItem($sword);
        $sender->getPlayer()->getInventory()->addItem($pluma);
    }
}