<?php

/**
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
 *
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\lifespan\command\overload;

use blugin\lib\command\BaseCommand;
use blugin\lib\command\overload\Overload;
use lifespan\Lifespan;
use pocketmine\command\CommandSender;

class ItemLifespanOverload extends LifespanOverload{
    public function __construct(BaseCommand $baseCommand){
        parent::__construct($baseCommand, "item");
    }

    /** @param mixed[] $args name => value */
    public function handle(CommandSender $sender, array $args, Overload $overload) : bool{
        Lifespan::getInstance()->setItemLifespan((int) ($args["seconds"] * 20));
        $this->sendMessage($sender, "success", [(string) $args["seconds"]]);
        return true;
    }
}