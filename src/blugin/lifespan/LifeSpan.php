<?php

namespace blugin\lifespan;

use pocketmine\plugin\PluginBase;
use blugin\lifespan\command\PoolCommand;
use blugin\lifespan\command\subcommands\{
  ItemSubCommand, ArrowSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};
use blugin\lifespan\util\Translation;
use blugin\lifespan\listener\EntityEventListener;

class LifeSpan extends PluginBase{

    /** @var LifeSpan */
    private static $instance = null;

    /** @return LifeSpan */
    public static function getInstance() : LifeSpan{
        return self::$instance;
    }

    /** @var PoolCommand */
    private $command;

    /** @var PluginLang */
    private $language;

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable() : void{
        $this->load();
        $this->getServer()->getPluginManager()->registerEvents(new EntityEventListener(), $this);
    }

    public function onDisable(){
        $this->save();
    }

    public function load() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->reloadConfig();

        $this->language = new PluginLang($this);
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

    public function save() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->saveConfig();
    }
    /**
     * @param string $name = ''
     *
     * @return PoolCommand
     */
    public function getCommand(string $name = '') : PoolCommand{
        return $this->command;
    }

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return $this->language;
    }

    /**
     * @return string
     */
    public function getSourceFolder() : string{
        $pharPath = \Phar::running();
        if (empty($pharPath)) {
            return dirname(__FILE__, 4) . DIRECTORY_SEPARATOR;
        } else {
            return $pharPath . DIRECTORY_SEPARATOR;
        }
    }
}
