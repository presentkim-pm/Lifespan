<?php

declare(strict_types=1);

namespace kim\present\lifespan\listener;

use kim\present\lifespan\Lifespan;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;

class EntityEventListener implements Listener{
	/** @var Lifespan */
	private $owner = null;

	/** @var \ReflectionProperty */
	private $property = null;

	/**
	 * EntityEventListener constructor.
	 *
	 * @param Lifespan $owner
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(Lifespan $owner){
		$this->owner = $owner;

		$reflection = new \ReflectionClass(Entity::class);
		$this->property = $reflection->getProperty("age");
		$this->property->setAccessible(true);
	}

	/**
	 * @param EntitySpawnEvent $event
	 */
	public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof ItemEntity){
			$this->property->setValue($entity, min(6000, max(-0x7fff, 6000 - $this->owner->getItemLifespan())));
		}elseif($entity instanceof Arrow){
			$this->property->setValue($entity, min(1200, max(-0x7fff, 1200 - $this->owner->getArrowLifespan())));
		}
	}
}