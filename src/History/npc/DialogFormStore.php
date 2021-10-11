<?php

namespace History\npc;

use InvalidArgumentException;
use pocketmine\entity\Entity;

class DialogFormStore {

    static private $forms = [];

    static public function getFormByEntity(Entity $entity): ?DialogForm {
        foreach(self::$forms as $form) {
            if($form->getEntity() === $entity) {
                return $form;
            }
        }
        return null;
    }

    static public function registerForm(DialogForm $form): void {
        if(in_array($form, self::$forms)) {
            throw new InvalidArgumentException("Trying to overwrite an already registered npc form");
        }
        self::$forms[] = $form;
    }

    static public function unregisterForm(DialogForm $form): void {
        if(($key = array_search($form, self::$forms)) !== false) {
            unset(self::$forms[$key]);
        } else {
            throw new InvalidArgumentException("Tried to unregister a dialog form that wasn't registered");
        }
    }
}