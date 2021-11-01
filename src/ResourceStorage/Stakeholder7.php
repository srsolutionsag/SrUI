<?php

namespace srag\Plugins\SrUI\ResourceStorage;

use ILIAS\ResourceStorage\Stakeholder\AbstractResourceStakeholder;

/**
 * Class Stakeholder7
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Stakeholder7 extends AbstractResourceStakeholder
{
    public function getId() : string
    {
        return "srui";
    }

    public function getOwnerOfNewResources() : int
    {
        return 6;
    }

}
