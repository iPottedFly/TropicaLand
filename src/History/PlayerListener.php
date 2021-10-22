<?php

namespace History;

use History\Banned\Data;
use History\Banned\Time;
use History\Items\admin\Vanish;
use pocketmine\event\player\{PlayerJoinEvent,
    PlayerQuitEvent,
    PlayerDeathEvent,
    PlayerBedEnterEvent,
    PlayerBedLeaveEvent,
    PlayerInteractEvent};
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\level\particle\EntityFlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat as A;
use pocketmine\Server;


class PlayerListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        #People with rank Normal
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setJoinMessage(A::GRAY . "[" . A::GREEN . "+" . A::GRAY . "]" . A::AQUA . " $name");

        #Only People with OP
        if($player->isOp()) {
            $player->sendMessage(A::DARK_PURPLE."Welcome Admin");
            $vanish = new Vanish();
            $player->getInventory()->addItem($vanish);
        }
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();

        $player->getLevel()->addParticle(new HeartParticle($player->asVector3()->add(0, 1.6, 0)), $player->getLevel()->getPlayers());
        $event->setQuitMessage(A::GRAY . "[" . A::RED . "-" . A::GRAY . "]" . A::AQUA . " $name");
        if($player->getLevel() === "gulag") {
            Data::addBan($name, "Disconnect in gulag", A::RED."Automatic Bot", false,"5m");
            Server::getInstance()->broadcastMessage(History::PREFIX.A::RESET.A::RED.$name.A::WHITE."Are banned for logout in gulag");
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $reason = ["La troleo", "Mongolo", "Menso"];
        $reason2 = ["Reventó ha", "Mandó al lobby ha", "Funó ha"];
        $positionx = $player->getFloorX();
        $positiony = $player->getFloorY();
        $positionz = $player->getFloorZ();
        $position = $positionx . " " . $positiony . " " . $positionz;
        if ($player instanceof Player) {
            if ($player->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
                $cause = $player->getLastDamageCause();
                $killer = $cause->getEntity();
                if($killer instanceof Player) {
                    $killer->sendMessage("");
                }
                $event->setDeathMessage(A::RED.$killer.A::GRAY." ".$reason2[array_rand($reason2)].$name);
            } else {
                if ($player->getLastDamageCause()->getCause() === null) return;
                switch ($player->getLastDamageCause()->getCause()) {
                    case EntityDamageEvent::CAUSE_DROWNING:
                    case EntityDamageEvent::CAUSE_FIRE:
                    case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                    case EntityDamageEvent::CAUSE_MAGIC:
                    case EntityDamageEvent::CAUSE_VOID:
                    case EntityDamageEvent::CAUSE_LAVA:
                    case EntityDamageEvent::CAUSE_FIRE_TICK:
                    case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                    case EntityDamageEvent::CAUSE_PROJECTILE:
                    case EntityDamageEvent::CAUSE_SUICIDE:
                    case EntityDamageEvent::CAUSE_SUFFOCATION:
                    case EntityDamageEvent::CAUSE_FALL:
                        $event->setDeathMessage(A::RED . $name . A::GRAY . " ha muerto, Razón: " . A::GOLD . $reason[array_rand($reason)]);
                        break;
                }
            }
        }
        $player->sendMessage(A::AQUA."Death Position: "."\n\n".A::WHITE.$position);
    }

    /** @var Player[] */
    private $in_bed_players = [];

    public function onEnter(PlayerBedEnterEvent $event): void {
        $player = $event->getPlayer();
        $server = Server::getInstance();
        $this->in_bed_players[] = $player;
        $server->broadcastMessage(A::GOLD . $player->getName() . " Está Durmiendo!, Durmiendo ahora: (" . count($this->in_bed_players) . "/" . count($server->getOnlinePlayers()) . ")");
        $player->getLevel()->addParticle(new EntityFlameParticle($player->asVector3()->add(0, 1.3, 0)), $player->getLevel()->getPlayers());
    }

    public function onLeave(PlayerBedLeaveEvent $event): void {
        $player = $event->getPlayer();
        $server = Server::getInstance();
        unset($this->in_bed_players[array_search($player, $this->in_bed_players, true)]);
        $server->broadcastMessage(A::GOLD . $player->getName() . " ha dejado la cama!, Durmiendo ahora: (" . count($this->in_bed_players) . "/" . count($server->getOnlinePlayers()) . ")");
    }

    public function onChangelvl(EntityLevelChangeEvent $event, Player $player) {
        $entity = $event->getEntity();
        $gulag = $player->getLevel()->getName();
        if($gulag === "gulag") {
            $inv = $player->getInventory();
            $inventory = [$inv];
            $entity->setHealth(20);
            $entity->setPosition(new Position("0", "80", "0", "gulag"));
        }
    }


    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $action = $event->getAction();
        $item = $event->getItem();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_AIR && $item->getId() === 288 && $item->hasCustomName() && $item->getCustomName() === A::BOLD . A::GOLD . "PlumaMagica") {

            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 160, 2));
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 160, 2));
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 160, 1));
            $player->getInventory()->getItemInHand()->setCustomName(A::GRAY . "Sin habilidad!");
            $item->setCount($item->getCount() -1);
        }
        if ($item->getId() === 106 && $item->hasCustomName() && $item->getCustomName() === A::BOLD . A::GREEN . "Vanish") {
            if($player->hasPermission("vanish.use")) {
                if($player->getAllowFlight()) {
                    $player->setFlying(false);
                    $player->setAllowFlight(false);
                    $player->sendMessage(A::RED."Vanish Desactivado");
                    $player->showPlayer($player);
                } else {
                    $player->sendMessage(A::GREEN."Vanish Activado");
                    $player->setAllowFlight(true);
                    $player->hidePlayer($player);
                }
            } else {
                $player->sendMessage(A::RED."No tienes permiso para vanish!");
            }
        }
    }
}