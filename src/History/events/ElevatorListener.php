<?php

namespace History\events;

use History\History;
use pocketmine\block\Air;
use pocketmine\block\SignPost;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;
use UnexpectedValueException;
use function floor;
use function strtolower;

final class ElevatorListener implements Listener{

    public function onSignChange(SignChangeEvent $event) : void{
        $lineIndex = History::getSignLine();
        $line = TextFormat::clean(TextFormat::colorize(strtolower($event->getLine($lineIndex))));

        if($line === strtolower(History::getSignUpText(true))){
            $line = History::getSignUpText();
        }elseif($line === strtolower(History::getSignDownText(true))){
            $line = History::getSignDownText();
        }else{
            return;
        }

        $player = $event->getPlayer();

        $event->setLine($lineIndex, $line);
        $player->sendMessage(History::getSignCreate());
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void{
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            return;
        }

        $block = $event->getBlock();
        if(!$block instanceof SignPost){
            return;
        }

        $level = $block->getLevel();
        if($level === null){
            return;
        }

        $x = (int) floor($block->getX());
        $y = (int) floor($block->getY());
        $z = (int) floor($block->getZ());

        if(($clickedSign = self::isLiftSign($x, $y, $z, $level)) === null){
            return;
        }

        $event->setCancelled();


        $line = TextFormat::clean($clickedSign->getLine(History::getSignLine()));
        $maxY = $level->getWorldHeight();
        $found = false;

        if($up = ($line === History::getSignUpText(true))){
            $y++;
            for(; $y <= $maxY; $y++){
                if($found = (self::isLiftSign($x, $y, $z, $level) !== null)){
                    break;
                }
            }
        }elseif($line === History::getSignDownText(true)){
            $y--;
            for(; $y >= 0; $y--){
                if($found = (self::isLiftSign($x, $y, $z, $level) !== null)){
                    break;
                }
            }
        }else{
            throw new UnexpectedValueException("Como puedes estar aqui?");
        }

        if($found){
            $y--;
            $safe = false;
            $maxY = $y - 1;
            for(; $y >= $maxY; $y--){
                $ground = $level->getBlockAt($x, $y, $z);
                if($safe = (!$ground instanceof Air)){
                    $y++;
                    break;
                }
            }
            $player = $event->getPlayer();
            if($safe){
                $player->sendMessage($up ? History::getUp() : History::getDown());
                $player->teleport(self::getCenterBlock(new Vector3($x, $y, $z)));
            }else{
                $player->sendMessage(History::getNotSafe());
            }

        }else{
            $player = $event->getPlayer();
            $player->sendMessage(History::getNotDest());
        }
    }

    public static function isLiftSign(int $x, int $y, int $z, Level $level) : ?Sign{
        $liftDest = $level->getTileAt($x, $y, $z);
        if(!$liftDest instanceof Sign){
            return null;
        }

        $line = TextFormat::clean($liftDest->getLine(History::getSignLine()));

        if($line !== History::getSignUpText(true) && $line !== History::getSignDownText(true)){
            return null;
        }

        return $liftDest;
    }

    private static function getCenterBlock(Vector3 $vector3) : Vector3{
        return $vector3->add(0.5, 0, 0.5);
    }
}