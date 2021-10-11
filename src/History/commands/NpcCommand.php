<?php

namespace History\commands;

use History\npc\Button;
use History\npc\DialogForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\utils\TextFormat as A;

class NpcCommand extends Command {

    public function __construct() {
        parent::__construct("npc", "Spawn a Npc", "/npc");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage(A::RED."¡ Error !");
            return;
        }

        #this is fase beta (Don't have functions)
        $nbt = Entity::createBaseNBT($sender, null, $sender->getYaw(), $sender->getPitch());
        $entity = Entity::createEntity("Creeper", $sender->getLevel(), $nbt);
        $entity->spawnToAll();

        $entity->setNameTag(A::AQUA."Tomi");


        $form = new DialogForm(A::GRAY."Hola! ¿Cómo estas?");

        $form->addButton(new Button("Bien!", function(Player $player) {
            $player->sendMessage(A::GOLD."Me alegro!");
        }));
        $form->setCloseListener(function(Player $player) {
            $player->sendMessage(A::GOLD."Nos vemos!");
        });

        $form->pairWithEntity($entity);
    }
}