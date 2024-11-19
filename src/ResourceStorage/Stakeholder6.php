<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\MainMenu\Storage\Resource\Stakeholder\AbstractResourceStakeholder;

/**
 * Class Stakeholder6
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Stakeholder6 extends AbstractResourceStakeholder
{
    public function getId(): string
    {
        return "srui";
    }
}
