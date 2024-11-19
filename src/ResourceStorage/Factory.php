<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\ResourceStorage;

use srag\Plugins\SrUI\ILIASVersion;

/**
 * Class Factory
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Factory
{
    protected ILIASVersion $ilias_version;

    public function __construct()
    {
        $this->ilias_version = new ILIASVersion(ILIAS_VERSION_NUMERIC);
    }

    public function storage(): ResourceStorage
    {
        if ($this->ilias_version->lessThanSeven()) {
            return new ResourceStorage6();
        }
        return new ResourceStorage7();
    }

    /**
     * @return Stakeholder6|Stakeholder7
     */
    public function stakeholder()
    {
        if ($this->ilias_version->lessThanSeven()) {
            return new Stakeholder6();
        }
        return new Stakeholder7();
    }
}
