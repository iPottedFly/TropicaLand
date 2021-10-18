<?php

namespace History\events;

use History\History;
use http\Exception\UnexpectedValueException;
use pocketmine\block\Air;
use pocketmine\block\SignPost;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Sign;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as A;
use function floor;
use function strtolower;

class ElevatorListener implements Listener {

    public static function isSSign(int $x, int $y, int $z, Level $level): ?Sign {
        $dest = $level->getTileAt($x, $y, $z);
        if(!$dest instanceof Sign) {
            return null;
        }

        $line = A::clean($dest->getLine(History::getSignLine()));
        if($line !== History::getSignUpText(true) && $line !== History::getSignDownText(true)) {
            return null;
        }
        return $dest;
    }

    public function onSignChange(SignChangeEvent $event) {
        $index = History::getSignLine();
        $line = A::clean(A::colorize(strtolower($event->getLine($index))));

        if($line === strtolower(History::getSignUpText(true))) {
            $line =History::getSignUpText();
        } elseif($line === strtolower(History::getSignDownText(true))) {
            $line = History::getSignDownText();
        } else {
            return;
        }
        $player = $event->getPlayer();
        $event->setLine($index, $line);
        $player->sendMessage(History::getSignCreate());
    }

    public function onInteract(PlayerInteractEvent $event) {
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            return;
        }

        $block = $event->getBlock();
        if(!$block instanceof SignPost) {
            return;
        }

        $level = $block->getLevel();
        if($level === null) {
            return;
        }

        $x = (int)floor($block->getX());
        $y = (int)floor($block->getY());
        $z = (int)floor($block->getZ());
        if(($clicksign = self::isSSign($x, $y, $z, $level)) === null) {
            return;
        }
        $event->setCancelled();
        $player = $event->getPlayer();

        $line = A::clean($clicksign->getLine(History::getSignLine()));
        $maxY = $level->getWorldHeight();
        $found = false;
        if($up = ($line === History::getSignUpText(true))) {
            $y++;
            for(; $y <= $maxY; $y++) {
                if($found = (self::isSSign($x, $y, $z, $level) !== null)) {
                    break;
                }
            }
        } elseif($line === History::getSignDownText(true)) {
            $y--;
            for(; $y >= 0; $y--) {
                if($found = (self::isSSign($x, $y, $z, $level) !== null)) {
                    break;
                }
            }
        } else {
            throw new UnexpectedValueException("How could you have been here?");
        }

        if($found) {
            $Y--;
            $safe = false;
            $maxY = $y -1;
            for(; $y >= $maxY; $y--) {
                $ground = $level->getBlockAt($x,$y,$z);
                if($safe = (!$ground instanceof Air)) {
                    $y++;
                    break;
                }
            }
            if($safe) {
                $player->sendMessage($up ? History::getUp() : History::getDown());
                $player->teleport(self::getCenterBlock(new Vector3($x, $y, $z)));
            } else {
                $player->sendMessage(History::getNotSafe());
            }
        } else {
            $player->sendMessage(History::getNotDest());
        }
    }

    private static function getCenterBlock(Vector3 $vector3): Vector3 {
        return $vector3->add(0.5, 0, 0.5);
    }
}