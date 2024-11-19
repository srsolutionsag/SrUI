<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Migration;

use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Configuration;

/**
 * Interface Migration
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface Migration
{
    public function appliesToVersionsPrior(): Version;

    public function perform(Configuration $configuration): void;
}
