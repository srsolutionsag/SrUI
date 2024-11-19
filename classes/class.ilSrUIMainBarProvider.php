<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuPluginProvider;

/**
 * Class ilSrUIMainBarProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIMainBarProvider extends AbstractStaticMainMenuPluginProvider
{
    /**
     * @return IdentificationInterface
     */
    public function getFooterIdentification(): IdentificationInterface
    {
        return $this->if->identifier('footer');
    }

    public function getStaticTopItems(): array
    {
        return [];
    }

    public function getStaticSubItems(): array
    {
        return [];
    }
}
