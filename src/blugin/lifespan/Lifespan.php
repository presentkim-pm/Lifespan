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

use blugin\lifespan\lang\PluginLang;
use blugin\lifespan\listener\EntityEventListener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class Lifespan extends PluginBase implements Listener{
    public const TYPE_ITEM = 0;
    public const TYPE_ARROW = 1;

    public const TAG_ITEM = "Item";
    public const TAG_ARROW = "Arrow";

    /** @var Lifespan */
    private static $instance = null;

    /** @var \ReflectionProperty */
    private $property = null;

    /**
     * @return Lifespan
     */
    public static function getInstance() : Lifespan{
        return self::$instance;
    }

    /** @var PluginLang */
    private $language;

    /** @var int[] */
    private $typeMap;

    /** @var string[] */
    private $typeTagMap = [
        self::TYPE_ITEM => "item",
        self::TYPE_ARROW => "arrow"
    ];

    /** @var int (short) */
    private $itemLifespan = 6000; //default: 5 minutes

    /** @var int (short) */
    private $arrowLifespan = 1200; //default: 60 seconds

    /**
     * Called when the plugin is loaded, before calling onEnable()
     */
    public function onLoad() : void{
        self::$instance = $this;

        $reflection = new \ReflectionClass(Entity::class);
        $this->property = $reflection->getProperty("age");
        $this->property->setAccessible(true);
    }

    /**
     * Called when the plugin is enabled
     */
    public function onEnable() : void{
        //Save default resources
        $this->saveResource("lang/eng/lang.ini", false);
        $this->saveResource("lang/kor/lang.ini", false);
        $this->saveResource("lang/language.list", false);

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
        $this->language = new PluginLang($this, $config->getNested("settings.language"));
        $this->getLogger()->info($this->language->translate("language.selected", [
            $this->language->getName(),
            $this->language->getLang()
        ]));

        //Register main command
        $command = new PluginCommand($config->getNested("command.name"), $this);
        $command->setPermission("lifespan.cmd");
        $command->setAliases($config->getNested("command.aliases"));
        $command->setUsage($this->language->translate("commands.lifespan.usage"));
        $command->setDescription($this->language->translate("commands.lifespan.description"));
        $this->getServer()->getCommandMap()->register($this->getName(), $command);

        //Load permission's default value from config
        $permission = PermissionManager::getInstance()->getPermission("lifespan.cmd");
        $defaultValue = $config->getNested("permission.main");
        if($permission !== null && $defaultValue !== null){
            $permission->setDefault(Permission::getByName($config->getNested("permission.main")));
        }

        //Load lifespan data from nbt
        if(file_exists($file = "{$this->getDataFolder()}data.dat")){
            try{
                /** @var CompoundTag $namedTag */
                $namedTag = (new BigEndianNBTStream())->readCompressed(file_get_contents($file));
                $this->itemLifespan = $namedTag->getShort(self::TAG_ITEM);
                $this->arrowLifespan = $namedTag->getShort(self::TAG_ARROW);
            }catch(\Throwable $e){
                rename($file, "{$file}.bak");
                $this->getLogger()->warning("Error occurred loading data.dat");
            }
        }

        //Register event listeners
        try{
            $this->getServer()->getPluginManager()->registerEvents(new EntityEventListener($this), $this);
        }catch(\ReflectionException $e){
            $this->setEnabled(false);
        }
    }

    /**
     * Called when the plugin is disabled
     * Use this to free open things and finish actions
     */
    public function onDisable() : void{
        //Save lifespan data to nbt
        try{
            file_put_contents("{$this->getDataFolder()}data.dat", (new BigEndianNBTStream())->writeCompressed(new CompoundTag("", [
                new ShortTag(self::TAG_ITEM, $this->itemLifespan),
                new ShortTag(self::TAG_ARROW, $this->arrowLifespan)
            ])));
        }catch(\Throwable $e){
            $this->getLogger()->warning("Error occurred saving data.dat");
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
     * @param EntitySpawnEvent $event
     */
    public function onEntitySpawnEvent(EntitySpawnEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof ItemEntity){
            $this->property->setValue($entity, min(6000, max(-0x7fff, 6000 - $this->getItemLifespan())));
        }elseif($entity instanceof Arrow){
            $this->property->setValue($entity, min(1200, max(-0x7fff, 1200 - $this->getArrowLifespan())));
        }
    }

    /**
     * @Override for multilingual support of the config file
     *
     * @return bool
     */
    public function saveDefaultConfig() : bool{
        $resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
        if($resource === null){
            $resource = $this->getResource("lang/" . PluginLang::FALLBACK_LANGUAGE . "/config.yml");
        }

        if(!file_exists($configFile = "{$this->getDataFolder()}config.yml")){
            $ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
            fclose($fp);
            fclose($resource);
            return $ret;
        }
        return false;
    }

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return $this->language;
    }

    /**
     * @return int
     */
    public function getItemLifespan() : int{
        return $this->itemLifespan;
    }

    /**
     * @param int $value (shrot)
     */
    public function setItemLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->itemLifespan = $value;
    }

    /**
     * @return int
     */
    public function getArrowLifespan() : int{
        return $this->arrowLifespan;
    }

    /**
     * @param int $value (shrot)
     */
    public function setArrowLifespan(int $value) : void{
        if($value < 0){
            throw new \InvalidArgumentException("Value {$value} is too small, it must be at least 0");
        }elseif($value > 0x7fff){
            throw new \InvalidArgumentException("Value {$value} is too big, it must be at most 0x7fff");
        }
        $this->arrowLifespan = $value;
    }
}
