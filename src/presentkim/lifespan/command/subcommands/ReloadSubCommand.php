<?php

namespace presentkim\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\lifespan\{
  LifeSpanMain as Plugin, util\Translation, command\SubCommand
};

class ReloadSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-lifespan-reload', 'lifespan.reload.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) {
        $this->owner->load();
        $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('success')));

        return true;
    }
}