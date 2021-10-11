<?php

namespace History\npc;

use Closure;
use JsonSerializable;
use pocketmine\Player;
use pocketmine\utils\Utils;

class Button implements JsonSerializable {
    /** @var string */
    private $name;

    /** @var string */
    private $text; //???
    /** @var null */
    private $data = null; //???
    /** @var int */
    private $mode = self::MODE_BUTTON; //???
    private const MODE_BUTTON = 0;
    private const MODE_ON_CLOSE = 1;

    /** @var int */
    private $type = self::TYPE_COMMAND; // ????

    private const TYPE_URL = 0; //???
    private const TYPE_COMMAND = 1;
    private const TYPE_INVALID = 2;

    /** @var Closure|null */
    private $submitListener;

    public function __construct(string $name, ?Closure $submitListener = null) {
        $this->name = $name;
        $this->setSubmitListener($submitListener);
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getSubmitListener(): ?Closure {
        return $this->submitListener;
    }

    public function setSubmitListener(?Closure $submitListener): void {
        if($submitListener !== null) {
            Utils::validateCallableSignature(function(Player $player) {}, $submitListener);
        }

        $this->submitListener = $submitListener;
    }

    public function executeSubmitListener(Player $player): void {
        if($this->submitListener !== null) {
            ($this->submitListener)($player);
        }
    }

    public function jsonSerialize(): array {
        return [
            "button_name" => $this->name,
            "text" => $this->text ?? "",
            "data" => $this->data,
            "mode" => $this->mode,
            "type" => $this->type
        ];
    }
}
