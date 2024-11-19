<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\ResourceStorage\Stakeholder\AbstractResourceStakeholder;

/**
 * Class Stakeholder7
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Stakeholder7 extends AbstractResourceStakeholder
{
    public function getId(): string
    {
        return "srui";
    }

    public function getOwnerOfNewResources(): int
    {
        return 6;
    }

    public function __construct()
    {
    }

}
