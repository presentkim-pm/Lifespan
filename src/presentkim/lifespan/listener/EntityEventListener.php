<?php

namespace presentkim\lifespan\listener;

use pocketmine\entity\{
  Entity, Item
};
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntitySpawnEvent;
use presentkim\lifespan\LifeSpanMain as Plugin;

class EntityEventListener implements Listener{

    /** @var Plugin */
    private $owner = null;

    /** @var \ReflectionProperty */
    private $property = null;

    public function __construct(){
        $this->owner = Plugin::getInstance();

        $reflection = new \ReflectionClass(Entity::class);
        $this->property = $reflection->getProperty('age');
        $this->property->setAccessible(true);
    }

    /** @param EntitySpawnEvent $event */
    public function onEntitySpawnEvent(EntitySpawnEvent $event){
        $entity = $event->getEntity();
        if ($entity instanceof Item) {
            $this->property->setValue($entity, 6000 - $this->owner->getConfig()->get('item-lifespan'));
        } elseif ($entity instanceof Arrow) {
            $this->property->setValue($entity, 1200 - $this->owner->getConfig()->get('arrow-lifespan'));
        }
    }
}