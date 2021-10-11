<?php

namespace History\npc;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\NpcRequestPacket;

class PacketListener implements Listener {

    private $responsePool = [];

    public function onPacketReciveEvent(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        $server = $player->getServer();

        if(!($packet instanceof NpcRequestPacket) or ($entity = $server->findEntity($packet->entityRuntimeId)) === null) {
            return;
        }

        $username = $player->getName();
        $logger = $server->getLogger();

        switch($packet->requestType) {
            case NpcRequestPacket::REQUEST_EXECUTE_ACTION:
                $logger->debug("Received a NpcRequestPacket action" . $packet->actionType);
                $this->responsePool[$username] = $packet->actionType;
                break;

            case NpcRequestPacket::REQUEST_EXECUTE_CLOSING_COMMANDS:
                $form = DialogFormStore::getFormByEntity($entity);
                if($form !== null) {
                    $form->handleResponse($player, $this->responsePool[$username] ?? null);
                    unset($this->responsePool[$username]);
                } else {
                    $logger->warning("Unhandled NpcRequestPacket for $username because there wasn't a registered form on the store");
                }
                break;
        }
    }

}