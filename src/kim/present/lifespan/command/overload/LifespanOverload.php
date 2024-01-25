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
use blugin\lib\command\handler\ICommandHandler;
use blugin\lib\command\overload\NamedOverload;
use blugin\lib\command\parameter\defaults\FloatParameter;

abstract class LifespanOverload extends NamedOverload implements ICommandHandler{
    public function __construct(BaseCommand $baseCommand, string $name){
        parent::__construct($baseCommand, $name);
        $this->addParamater((new FloatParameter("seconds"))->setMin(0)->setMax(0x7fff));
        $this->setHandler($this);
    }
}