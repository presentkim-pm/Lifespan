<?php

namespace presentkim\lifespan;

use pocketmine\plugin\PluginBase;
use presentkim\lifespan\util\Translation;
use presentkim\lifespan\listener\EntityEventListener;
use presentkim\lifespan\command\PoolCommand;
use presentkim\lifespan\command\subcommands\{
  ItemSubCommand, ArrowSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};

class LifeSpanMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var PoolCommand */
    private $command;

    /** @return self */
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        if (self::$instance === null) {
            self::$instance = $this;
            $this->getServer()->getLoader()->loadClass('presentkim\lifespan\util\Utils');
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable(){
        $this->load();
        $this->getServer()->getPluginManager()->registerEvents(new EntityEventListener(), $this);
    }

    public function onDisable(){
        $this->save();
    }

    public function load(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->reloadConfig();

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
            fclose($fp);
            Translation::loadFromContents($contents);
        } else {
            Translation::load($langfilename);
        }

        $this->reloadCommand();
    }

    public function save(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->saveConfig();
    }

    public function reloadCommand(){
        if ($this->command == null) {
            $this->command = new PoolCommand($this, 'lifespan');
            $this->command->createSubCommand(ItemSubCommand::class);
            $this->command->createSubCommand(ArrowSubCommand::class);
            $this->command->createSubCommand(LangSubCommand::class);
            $this->command->createSubCommand(ReloadSubCommand::class);
            $this->command->createSubCommand(SaveSubCommand::class);
        }
        $this->command->updateTranslation();
        $this->command->updateSudCommandTranslation();
        if ($this->command->isRegistered()) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
    }
}
