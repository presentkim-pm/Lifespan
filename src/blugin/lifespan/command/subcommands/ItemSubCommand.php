<?php

namespace blugin\lifespan\command\subcommands;

use pocketmine\command\CommandSender;
use blugin\lifespan\LifeSpan as Plugin;
use blugin\lifespan\command\{
  PoolCommand, SubCommand
};
use blugin\lifespan\util\{
  Translation, Utils
};

class ItemSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'item');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[0])) {
            $lifespan = Utils::toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            if ($lifespan === null) {
                $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
            } else {
                $this->plugin->getConfig()->set('item-lifespan', $lifespan);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $lifespan));
            }
            return true;
        }
        return false;
    }
}