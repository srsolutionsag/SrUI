<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\UI\SettingsTo;

use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class Meta
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Meta
{
    protected $meta;

    public function __construct()
    {
        global $DIC;

        $this->meta = $DIC->globalScreen()->layout()->meta();
    }

    public function transfer(Artifact $artifact): void
    {
    }
}
