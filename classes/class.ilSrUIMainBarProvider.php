<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuPluginProvider;

/**
 * Class ilSrUIMainBarProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIMainBarProvider extends AbstractStaticMainMenuPluginProvider
{

    /**
     * @return \ILIAS\GlobalScreen\Identification\IdentificationInterface
     */
    public function getFooterIdentification() : \ILIAS\GlobalScreen\Identification\IdentificationInterface
    {
        return $this->if->identifier('footer');
    }

    public function getStaticTopItems() : array
    {
        return [
            $this->mainmenu->topParentItem($this->getFooterIdentification())
                           ->withTitle('Footer Container')
                           ->withAvailableCallable(static function () {
                               return true;
                           })
                           ->withVisibilityCallable(static function () {
                               return false;
                           }),
            $this->mainmenu->topParentItem($this->if->identifier('metabar'))
                           ->withTitle('MetaBar Container')
                           ->withAvailableCallable(static function () {
                               return true;
                           })
                           ->withVisibilityCallable(static function () {
                               return false;
                           })
        ];
    }

    public function getStaticSubItems() : array
    {
        return [];
    }

}
