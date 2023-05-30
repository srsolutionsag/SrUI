<?php

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
