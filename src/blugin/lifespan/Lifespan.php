<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lifespan;

use blugin\lib\lang\LanguageTrait;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Limits;
use pocketmine\utils\SingletonTrait;

class Lifespan extends PluginBase implements Listener{
    use SingletonTrait;
    use LanguageTrait;

    public const TYPE_ITEM = 0;
    public const TYPE_ARROW = 1;

    public const TAG_ITEM = "Item";
    public const TAG_ARROW = "Arrow";

    /** @var \ReflectionProperty */
    private $property = null;

    /** @var int[] */
    private $typeMap;

    /** @var string[] */
    private $typeTagMap = [
        self::TYPE_ITEM => "item",
        self::TYPE_ARROW => "arrow"
    ];

    /** @var int (short) */
    private $itemLifespan = ItemEntity::DEFAULT_DESPAWN_DELAY; //default: 5 minutes

    /** @var int (short) */
    private $arrowLifespan = 1200; //default: 60 seconds

    /**
     * Called when the plugin is loaded, before calling onEnable()
     *
     * @throws \ReflectionException
     */
    public function onLoad() : void{
        self::setInstance($this);

        $reflection = new \ReflectionClass(Arrow::class);
        $this->property = $reflection->getProperty("collideTicks");
        $this->property->setAccessible(true);
    }

    /**
     * Called when the plugin is enabled
     */
    public function onEnable() : void{
        //Load config file
        $config = $this->getConfig();

        //Load type map from config file
        $this->typeMap = [];
        foreach($this->typeTagMap as $type => $tag){
            $this->typeMap[strtolower($config->getNested("command.children.{$tag}.name"))] = $type;
            foreach($config->getNested("command.children.{$tag}.aliases") as $key => $aliases){
                $this->typeMap[strtolower($aliases)] = $type;
            }
        }

        //Load language file
        $this->loadLanguage($config->getNested("settings.language"));
        $this->getLogger()->info($this->language->translate("language.selected", [
            $this->language->getName(),
            $this->language->getLang()
        ]));

        //Register main command
        $command = new PluginCommand($config->getNested("command.name"), $this, $this);
        $command->setPermission("lifespan.cmd");
        $command->setAliases($config->getNested("command.aliases"));
        $command->setUsage($this->language->translate("commands.lifespan.usage"));
        $command->setDescription($this->language->translate("commands.lifespan.description"));
        $this->getServer()->getCommandMap()->register($this->getName(), $command);

        //Load permission's default value from config
        $permission = PermissionManager::getInstance()->getPermission("lifespan.cmd");
        $defaultValue = $config->getNested("permission.main");
        if($permission !== null && $defaultValue !== null){
            $permission->setDefault($config->getNested("permission.main"));
        }

        //Load lifespan data from nbt
        $file = "{$this->getDataFolder()}data.dat";
        if(file_exists($file)){
            $contents = @file_get_contents($file);
            if($contents === false)
                throw new \RuntimeException("Failed to read LifeSpan data file \"$file\" (permission denied?)");

            $decompressed = @zlib_decode($contents);
            if($decompressed === false){
                throw new \RuntimeException("Failed to decompress raw data for LifeSpan");
            }

            try{
                $tag = (new BigEndianNbtSerializer())->read($decompressed)->mustGetCompoundTag();
            }catch(NbtDataException $e){
                throw new \RuntimeException("Failed to decode NBT data for LifeSpan");
            }

            if($tag instanceof CompoundTag){
                $this->itemLifespan = $tag->getShort(self::TAG_ITEM);
                $this->arrowLifespan = $tag->getShort(self::TAG_ARROW);
            }else{
                throw new \RuntimeException("The file is not in the NBT-CompoundTag format : $file");
            }
        }

        //Register event listeners
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * Called when the plugin is disabled
     * Use this to free open things and finish actions
     */
    public function onDisable() : void{
        //Save lifespan data to nbt
        $tag = CompoundTag::create();
        $tag->setShort(self::TAG_ITEM, $this->itemLifespan);
        $tag->setShort(self::TAG_ARROW, $this->arrowLifespan);

        $nbt = new BigEndianNbtSerializer();
        try{
            file_put_contents("{$this->getDataFolder()}data.dat", zlib_encode($nbt->write(new TreeRoot($tag)), ZLIB_ENCODING_GZIP));
        }catch(\ErrorException $e){
            $this->getLogger()->critical($this->getServer()->getLanguage()->translateString("pocketmine.data.saveError", [
                "LifeSpan-data",
                $e->getMessage()
            ]));
            $this->getLogger()->logException($e);
        }
    }

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(isset($args[1])){
            $type = $this->typeMap[strtolower($args[0])] ?? null;
            if($type === null){
                $sender->sendMessage($this->language->translate("commands.lifespan.failure.invalid", [$args[0]]));
            }elseif(!is_numeric($args[1])){
                $sender->sendMessage($this->language->translate("commands.generic.num.notNumber", [$args[1]]));
            }else{
                $lifespan = (int) $args[1];
                if($lifespan < 0){
                    $sender->sendMessage($this->language->translate("commands.generic.num.tooSmall", [
                        (string) $lifespan,
                        (string) 0
                    ]));
                }elseif($lifespan > 0x7fff){
                    $sender->sendMessage($this->language->translate("commands.generic.num.tooBig", [
                        (string) $lifespan,
                        (string) 0x7fff
                    ]));
                }else{
                    $type ? $this->setArrowLifespan($lifespan) : $this->setItemLifespan($lifespan);
                    $sender->sendMessage($this->language->translate("commands.lifespan.success", [
                        $this->getConfig()->getNested("command.children.{$this->typeTagMap[$type]}.name"),
                        (string) $lifespan
                    ]));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @priority MONITOR
     *
     * @param EntitySpawnEvent $event
     */
    public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof ItemEntity){
            $entity->setDespawnDelay(min(Limits::INT16_MAX, max(0, $this->getItemLifespan())));
        }elseif($entity instanceof Arrow){
            $this->property->setValue($entity, min(Limits::INT16_MAX, max(0, $this->getArrowLifespan())));
        }
    }

    /** @return int */
    public function getItemLifespan() : int{
        return $this->itemLifespan;
    }

    /** @param int $value (short) */
    public function setItemLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > Limits::INT16_MAX){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->itemLifespan = $value;
    }

    /** @return int */
    public function getArrowLifespan() : int{
        return $this->arrowLifespan;
    }

    /** @param int $value (short) */
    public function setArrowLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > Limits::INT16_MAX){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->arrowLifespan = $value;
    }
}
