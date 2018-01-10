<?php

namespace presentkim\lifespan\listener;

use pocketmine\entity\Item;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\{
  entity\EntitySpawnEvent, Listener
};
use presentkim\lifespan\LifeSpanMain as Plugin;

class EntityEventListener implements Listener{

    /** @var Plugin */
    private $owner = null;

    public function __construct(){
        $this->owner = Plugin::getInstance();
    }

    /** @param EntitySpawnEvent $event */
    public function onEntitySpawnEvent(EntitySpawnEvent $event){
        $entity = $event->getEntity();
        if ($entity instanceof Item) {
            $lifespan = 6000 - $this->owner->getConfig()->get('item-lifespan');
            $reflection = new \ReflectionClass("\\pocketmine\\entity\\projectile\\Arrow");
            var_dump('Item: ' . $lifespan);
        } elseif ($entity instanceof Arrow) {
            $lifespan = 1200 - $this->owner->getConfig()->get('arrow-lifespan');
            $reflection = new \ReflectionClass("\\pocketmine\\entity\\Item");
            var_dump('Arrow: ' . $lifespan);
        } else {
            return;
        }
        $property = $reflection->getProperty('age');
        $property->setAccessible(true);
        $property->setValue($entity, $lifespan);
    }
}