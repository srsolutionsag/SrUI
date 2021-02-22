<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\UI\Component\Layout\Page\Page;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Login;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\LoginPageSettings;
use srag\Plugins\SrUI\Artifact\Artifact;
use srag\Plugins\SrUI\ResourceStorage\Factory;

/**
 * Class ilSrUIModificationProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    /**
     * @param Artifact $settings
     * @return Closure
     */
    private function getLoginPageModification() : Closure
    {
        $settings = new Artifact('sr_ui_setting');

        $login_page_settings = new LoginPageSettings();
        $login_page_settings->setCenterLogin((bool) $settings[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE]);
        if ($login_page_settings->isCenterLogin()) {
            $this->globalScreen()->layout()->meta()->addInlineCss((string) $settings[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE_CSS]);
        }
        $login_page_settings->setShowHeader((bool) $settings[ilSrUILoginPageConfigGUI::F_SHOW_HEADER]);
        $login_page_settings->setShowFooter((bool) $settings[ilSrUILoginPageConfigGUI::F_SHOW_FOOTER]);
        $login_page_settings->setShowMainBar((bool) $settings[ilSrUILoginPageConfigGUI::F_SHOW_MAIN_BAR]);

        if ($settings[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE]) {
            $s = Factory::storage()->getDataURL((string) $settings[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE][0]);
            if (!empty($s)) {
                $login_page_settings->setBackgroundImage($s);
            }
        }

        return static function (PagePartProvider $parts) use ($login_page_settings) : Page {
            return new Login(
                $login_page_settings,
                [$parts->getContent()],
                $parts->getMetaBar(),
                $parts->getMainBar(),
                $parts->getBreadCrumbs(),
                $parts->getLogo(),
                $parts->getFooter(),
                $parts->getTitle(),
                $parts->getShortTitle(),
                $parts->getViewTitle()
            );
        };
    }

    public function isInterestedInContexts() : ContextCollection
    {
        return $this->context_collection->main()->external();
    }

    public function getPageBuilderDecorator(CalledContexts $screen_context_stack) : ?PageBuilderModification
    {
        if ($screen_context_stack->current()->getAdditionalData()->is(ilSrUIPlugin::OUTSIDE_ILIAS, true)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->factory->page()
                                 ->withLowPriority()
                                 ->withModification($this->getLoginPageModification());
        }

        return null;
    }

}
