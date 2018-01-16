<?php

namespace presentkim\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\lifespan\{
  command\PoolCommand, LifeSpanMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\lifespan\util\toInt;

class ArrowSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'arrow');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if (isset($args[0])) {
            $lifespan = toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            if ($lifespan === null) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('arrow-lifespan', $lifespan);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $lifespan));
            }
            return true;
        }
        return false;
    }
}