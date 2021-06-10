<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\Layout\Factory\FooterModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LayoutModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LogoModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\ShortTitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\TitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\UI\Component\Layout\Page\Page;
use ILIAS\UI\Implementation\Component\Image\Image;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Login;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\LoginPageSettings;
use ILIAS\UI\Implementation\Component\MainControls\Footer;
use ILIAS\UICore\PageContentProvider;
use srag\Plugins\SrUI\Artifact\Artifact;
use srag\Plugins\SrUI\ResourceStorage\Factory;

/**
 * Class ilSrUIModificationProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    const PRIORITY = 60;
    const PLUGIN_BASE = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/";

    /**
     * @return Artifact
     */
    private function getArtifact() : Artifact
    {
        static $settings;
        if (!$settings) {
            $settings = Artifact::getInstance('sr_ui_setting');
        }
        return $settings;
    }

    /**
     * @param Artifact $settings
     * @return Closure
     */
    private function getLoginPageModification() : Closure
    {
        $settings = $this->getArtifact();

        $login_page_settings = new LoginPageSettings();
        $login_page_settings->setCenterLogin((bool) $settings[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE]);
        if ($login_page_settings->isCenterLogin()) {
            $this->globalScreen()->layout()->meta()->addInlineCss((string) $settings[ilSrUILoginPageConfigGUI::F_CENTERED_LOGIN_PAGE_CSS]);
        }
        $login_page_settings->setShowHeader((bool) ($settings[ilSrUILoginPageConfigGUI::F_SHOW_HEADER] ?? true));
        $login_page_settings->setShowFooter((bool) ($settings[ilSrUILoginPageConfigGUI::F_SHOW_FOOTER] ?? true));
        $login_page_settings->setShowMainBar((bool) ($settings[ilSrUILoginPageConfigGUI::F_SHOW_MAIN_BAR] ?? true));

        if ($settings[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE]) {
            $s = Factory::storage()->getDataURL((string) $settings[ilSrUILoginPageConfigGUI::F_BACKGROUND_IMAGE][0]);
            if (!empty($s)) {
                $login_page_settings->setBackgroundImage($s);
            }
        }

        if (!$login_page_settings->isShowHeader()) {
            $this->globalScreen()->layout()->meta()->addCss(self::PLUGIN_BASE . 'templates/no_header.css');
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
        return $this->context_collection->main()->main();
    }

    public function getPageBuilderDecorator(CalledContexts $screen_context_stack) : ?PageBuilderModification
    {
        $settings = $this->getArtifact();
        // Cards
        if ((bool) $settings[ilSrUIContentConfigGUI::F_FANCY_CARDS]) {
            $this->dic->globalScreen()->layout()->meta()->addCss(self::PLUGIN_BASE . "templates/cards.css");
        }

        // Darkmode
        if ((bool) $settings[ilSrUIHeaderConfigGUI::F_DARKMODE]) {
            $this->dic->globalScreen()->layout()->meta()->addCss(self::PLUGIN_BASE . "templates/darkmode.css");
        }

        // Login Page
        if ($screen_context_stack->current()->getAdditionalData()->is(ilSrUIPlugin::ON_LOGIN_PAGE, true)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->factory->page()
                                 ->withLowPriority()
                                 ->withModification($this->getLoginPageModification());
        }

        return null;
    }

    private function setPriority(LayoutModification $m) : LayoutModification
    {
        try {
            return $m->withPriority(self::PRIORITY);
        } catch (Throwable $t) {
            return $m->withHighPriority();
        }
    }

    public function getLogoModification(CalledContexts $screen_context_stack) : ?LogoModification
    {
        $settings = $this->getArtifact();
        if ((bool) $settings[ilSrUIHeaderConfigGUI::F_ICON]) {
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo img { width: {$settings[ilSrUIHeaderConfigGUI::F_ICON_WIDTH]};}");
            $s = Factory::storage()->getDataURL((string) $settings[ilSrUIHeaderConfigGUI::F_ICON][0]);
            if (!empty($s)) {
                $logo_modification = $this->factory->logo();
                $logo_modification = $this->setPriority($logo_modification);

                return $logo_modification->withModification(function (Image $image) use ($s) {
                    return $image->withSource($s);
                });

            }

        }
        return null;
    }

    public function getFooterModification(CalledContexts $screen_context_stack) : ?FooterModification
    {
        $settings = $this->getArtifact();
        if ((bool) $settings[ilSrUIFooterConfigGUI::F_SHOW_RENDERED_BY]) {

            $footer_modification = $this->factory->footer();
            $footer_modification = $this->setPriority($footer_modification);

            return $footer_modification
                ->withModification(function () : \ILIAS\UI\Component\MainControls\Footer {
                    $pageContentProvider = new PageContentProvider($this->dic);
                    $footer              = $pageContentProvider->getFooterModification($this->dic->globalScreen()->tool()->context()->stack())->getModification();
                    /**
                     * @var $f Footer
                     */
                    $f = $footer();

                    $hostname      = gethostname();
                    $ilias_version = ILIAS_VERSION_NUMERIC;
                    preg_match('/\d+\.\d+.\d+/', PHP_VERSION, $php_version);

                    $links           = $f->getLinks();
                    $mmprovider = new ilSrUIMainBarProvider($this->dic, $this->plugin);


                    $replaced_footer = $this->dic->ui()->factory()->mainControls()->footer(
                        $links,
                        "Rendered by " . $hostname . " - " . $ilias_version . " - " . $php_version[0]
                    );
                    if ($f->getPermanentURL()) {
                        $replaced_footer = $replaced_footer->withPermanentURL($f->getPermanentURL());
                    }
                    return $replaced_footer;
                });
        }
        return null;
    }

    public function getTitleModification(CalledContexts $screen_context_stack) : ?TitleModification
    {
        $settings = $this->getArtifact();
        if ($settings->offsetExists(ilSrUIHeaderConfigGUI::F_HEADER_TITLE)) {
            $title_modification = $this->factory->title();
            $title_modification = $this->setPriority($title_modification);

            return $title_modification->withModification(function (string $f) use ($settings) : string {
                return (string) $settings[ilSrUIHeaderConfigGUI::F_HEADER_TITLE];
            });
        }
        return null;
    }

    public function getShortTitleModification(CalledContexts $screen_context_stack) : ?ShortTitleModification
    {
        $settings = $this->getArtifact();
        if ($settings->offsetExists(ilSrUIHeaderConfigGUI::F_HEADER_TITLE)) {
            $short_title_modification = $this->factory->short_title();
            $short_title_modification = $this->setPriority($short_title_modification);

            return $short_title_modification->withModification(function (string $f) use ($settings) : string {
                return (string) $settings[ilSrUIHeaderConfigGUI::F_HEADER_TITLE];
            });
        }
        return null;
    }

}
