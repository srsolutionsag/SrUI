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
    public const PLUGIN_BASE = __DIR__ . '/../';
    public const TEMPLATES_BASE = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/";
    public const OUTSIDE_ILIAS = 'anon';
    public const ON_LOGIN_PAGE = 'on_login_page';
    
    protected function onBoarding() : void
    {
        $a = srag\Plugins\SrUI\Artifact\Artifact::getInstance('sr_ui_setting');
        $a->clear();
        
        $s = Factory::storage();
        
        $a[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE]     = true;
        $a[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE_CSS] = file_get_contents(self::TEMPLATES_BASE . "login.css");
        $a[ilSrUILoginPageConfigGUI::F_SHOW_FOOTER]             = false;
        $a[ilSrUILoginPageConfigGUI::F_SHOW_MAIN_BAR]           = false;
        $a[ilSrUILoginPageConfigGUI::F_SHOW_HEADER]             = false;
        $a[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE]        = [
            $s->fromPath(
                self::TEMPLATES_BASE . "Subtle-Prism.svg",
                ilMimeTypeUtil::IMAGE__SVG_XML
            )
        ];
        $a[ilSrUIHeaderConfigGUI::F_ICON]                       = [
            $s->fromPath(
                self::TEMPLATES_BASE . "HeaderIconPlus.svg",
                ilMimeTypeUtil::IMAGE__SVG_XML)
        ];
        $a[ilSrUIHeaderConfigGUI::F_ICON_WIDTH]                 = '45px';
        $a[ilSrUIHeaderConfigGUI::F_HEADER_TITLE]               = 'ILIAS+ 6';
        $a[ilSrUIHeaderConfigGUI::F_DARKMODE]                   = true;
        $a[ilSrUIFooterConfigGUI::F_SHOW_RENDERED_BY]           = true;
        $a[ilSrUIContentConfigGUI::F_FANCY_TABS]                = false;
        $a[ilSrUIContentConfigGUI::F_FANCY_CARDS]               = true;
        $a->save();
    }
    
    public function getPluginName()
    {
        return 'SrUI';
    }
    
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
//        $this->onBoarding();
        return PageLoaderDetector::exchange();
    }
    
    public function __construct()
    {
        global $DIC;
        
        parent::__construct();
        try {
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::OUTSIDE_ILIAS,
                $this->isOutsideILIAS($DIC)
            );
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::ON_LOGIN_PAGE,
                $this->isOnLoginPage()
            );
        } catch (LogicException $l) {
        
        }
        
        $this->provider_collection->setModificationProvider(new ilSrUIModificationProvider($DIC, $this));
        $this->provider_collection->setMainBarProvider(new ilSrUIMainBarProvider($DIC, $this));
    }
    
    /**
     * @param Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(Container $DIC) : bool
    {
        return (
            ($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0)
            && strtolower($DIC->http()->request()->getQueryParams()["baseClass"]) !== strtolower(ilRepositoryGUI::class)
        
        );
    }
    
    public function isOnLoginPage() : bool
    {
        return strpos($_SERVER['SCRIPT_NAME'], 'login.php') !== false;
    }
    
    protected function afterInstall()
    {
        $this->onBoarding();
    }
    
}
