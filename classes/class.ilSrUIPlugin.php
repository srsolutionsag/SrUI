<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\DI\Container;
use srag\Plugins\SrUI\ResourceStorage\Factory;
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

    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return PageLoaderDetector::exchange();
    }

    public function __construct()
    {
        global $DIC;

        parent::__construct();
        try {
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(self::OUTSIDE_ILIAS, $this->isOutsideILIAS($DIC));
        } catch (LogicException $l) {

        }

        $this->provider_collection->setModificationProvider(new ilSrUIModificationProvider($DIC, $this));
    }

    /**
     * @param Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(Container $DIC) : bool
    {
        return (($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0) && strtolower($DIC->http()->request()->getQueryParams()["baseClass"]) !== strtolower(ilRepositoryGUI::class));
    }

    protected function afterInstall()
    {
        $base = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/";
        $a    = new srag\Plugins\SrUI\Artifact\Artifact('sr_ui_setting');
        $a->clear();

        $s = Factory::storage();

        $a[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE]     = true;
        $a[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE_CSS] = file_get_contents($base . "login.css");
        $a[ilSrUILoginPageConfigGUI::F_SHOW_FOOTER]             = true;
        $a[ilSrUILoginPageConfigGUI::F_SHOW_MAIN_BAR]           = false;
        $a[ilSrUILoginPageConfigGUI::F_SHOW_HEADER]             = true;
        $a[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE]        = [$s->fromPath($base . "Tortoise-Shell.svg", ilMimeTypeUtil::IMAGE__SVG_XML)];
        $a[ilSrUIHeaderConfigGUI::F_ICON]                       = [$s->fromPath($base . "HeaderIconPlus.svg", ilMimeTypeUtil::IMAGE__SVG_XML)];
        $a[ilSrUIHeaderConfigGUI::F_ICON_WIDTH]                 = '45px';
        $a[ilSrUIFooterConfigGUI::F_SHOW_RENDERED_BY]           = true;
        $a->save();
    }

}
