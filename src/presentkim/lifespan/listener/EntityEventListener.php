<?php

namespace presentkim\lifespan\listener;

use pocketmine\entity\{
    Entity, Item, projectile\Arrow
};
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
            $reflection = new \ReflectionClass(Item::class);
            $lifespan = 6000 - $this->owner->getConfig()->get('item-lifespan');
        } elseif ($entity instanceof Arrow) {
            $reflection = new \ReflectionClass(Arrow::class);
            $lifespan = 1200 - $this->owner->getConfig()->get('arrow-lifespan');
        } else {
            return;
        }
        $property = $reflection->getProperty('age');
        $property->setAccessible(true);
        $property->setValue($entity, $lifespan);
    }
}