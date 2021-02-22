<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use srag\Plugins\SrUI\UI\Loader\PageLoaderDetector;

/**
 * Class ilSrUIPlugin
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIPlugin extends ilUserInterfaceHookPlugin
{
    public const  LOGIN_TEMPLATE_WRAPPER = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/tpl.login_page.html';
    public const LOGIN_CSS = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/login.css";
    const OUTSIDE_ILIAS = 'anon';

    public function getPluginName()
    {
        return 'SrUI';
    }

    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic) : Closure
    {
        return PageLoaderDetector::exchange();
    }

    public function __construct()
    {
        global $DIC;

        parent::__construct();
        $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(self::OUTSIDE_ILIAS, $this->isOutsideILIAS($DIC));

        $this->provider_collection->setModificationProvider(new ilSrUIModificationProvider($DIC, $this));
    }

    /**
     * @param \ILIAS\DI\Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(\ILIAS\DI\Container $DIC) : bool
    {
        return (($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0) && strtolower($DIC->http()->request()->getQueryParams()["baseClass"]) !== strtolower(ilRepositoryGUI::class));
    }

}
