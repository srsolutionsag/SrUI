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
use srag\Plugins\SrUI\UI\PageBuilderStack;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;
use srag\Plugins\SrUI\Config\Footer\ColorConfig;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;

/**
 * Class ilSrUIModificationProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    const PRIORITY = 60;

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


    public function isInterestedInContexts() : ContextCollection
    {
        return $this->context_collection->main()->main();
    }

    public function getPageBuilderDecorator(CalledContexts $screen_context_stack) : ?PageBuilderModification
    {
        $settings = $this->getArtifact();
        // Non-Productive Systems
        if ((bool) $settings[ProdServerConfig::F_PROD_SYSTEM]) {
            $prod_system_settings = $settings[ProdServerConfig::F_PROD_SYSTEM];
            $hostnames = explode(",", $prod_system_settings[ProdServerConfig::F_PROD_HOSTNAMES]);
            if (!in_array(gethostname(), $hostnames)) {
                // Send X-Robots-Tag: noindex
                if ((bool) $settings[ProdServerConfig::F_PROD_SYSTEM][ProdServerConfig::F_SEND_NON_INDEX_HEADERS]) {
                    $repsonse = $this->dic->http()->response()->withAddedHeader('X-Robots-Tag', 'noindex');
                    $this->dic->http()->saveResponse($repsonse);
                    $this->dic->http()->sendResponse();
                }
            }
        }

        // Cards
        if ((bool) $settings[\srag\Plugins\SrUI\Config\Content\Config::F_FANCY_CARDS]) {
            $this->dic->globalScreen()->layout()->meta()->addCss(ilSrUIPlugin::TEMPLATES_BASE . "cards.css");
        }

        // Darkmode
        if ((bool) $settings[ilSrUIHeaderConfigGUI::F_DARKMODE]) {
            $this->dic->globalScreen()->layout()->meta()->addCss(ilSrUIPlugin::TEMPLATES_BASE . "darkmode.css");
        }

        // Header-Colors
        if ((bool) $settings[HeaderColorsConfiguration::NS]) {
            $header_bg_color = $settings[HeaderColorsConfiguration::F_HEADER_BG_COLOR];
            $bc_bg_color = $settings[HeaderColorsConfiguration::F_HEADER_BC_BG_COLOR];
            $bc_text_color = $settings[HeaderColorsConfiguration::F_HEADER_BC_TEXT_COLOR];
            $this->dic->globalScreen()->layout()->meta()->addInlineCss("
            div.header-inner {
                background-color: $header_bg_color;
            }
            div.breadcrumbs, nav.breadcrumb_wrapper {
                background-color: $bc_bg_color;
                border-color: $bc_bg_color;
            }
            div.breadcrumbs, nav.breadcrumb_wrapper a {
                color: $bc_text_color;
            }
            ");
        }

        // Footer Colors
        if ((bool) $settings[ColorConfig::F_FOOTER_COLORS]) {
            $footer_bg_color = $settings[ColorConfig::F_FOOTER_BG_COLOR];
            $footer_text_color = $settings[ColorConfig::F_FOOTER_TEXT_COLOR];

            $this->dic->globalScreen()->layout()->meta()->addInlineCss("
            footer, div.il-maincontrols-footer {
                background-color: $footer_bg_color !important;
                color: $footer_text_color;
            }
            
            .il-maincontrols-footer .il-footer-links li a {
                color: $footer_text_color !important;
            }
           
            ");
        }

        // Page
        $builder = new PageBuilderStack($this->dic, $settings);
        return $builder->getAsModification();
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
        if ((bool) $settings[HeaderLogoConfiguration::F_ICON]) {
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo img { width: {$settings[HeaderLogoConfiguration::F_ICON_WIDTH]};}");
            $s = Factory::storage()->getDataURL((string) $settings[HeaderLogoConfiguration::F_ICON][0]);
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
        if ((bool) $settings[AdditionalInfoConfig::F_SHOW_RENDERED_BY]) {

            $footer_modification = $this->factory->footer();
            $footer_modification = $this->setPriority($footer_modification);

            return $footer_modification
                ->withModification(function () : \ILIAS\UI\Component\MainControls\Footer {
                    $pageContentProvider = new PageContentProvider($this->dic);
                    $footer = $pageContentProvider->getFooterModification($this->dic->globalScreen()->tool()->context()->stack())->getModification();
                    /**
                     * @var $f Footer
                     */
                    $f = $footer();

                    $hostname = gethostname();
                    $ilias_version = ILIAS_VERSION_NUMERIC;
                    preg_match('/\d+\.\d+.\d+/', PHP_VERSION, $php_version);

                    $links = $f->getLinks();
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
        if ($settings->offsetExists(HeaderTitleConfiguration::F_HEADER_TITLE)) {
            $title_modification = $this->factory->title();
            $title_modification = $this->setPriority($title_modification);

            return $title_modification->withModification(function (string $f) use ($settings) : string {
                return (string) $settings[HeaderTitleConfiguration::F_HEADER_TITLE];
            });
        }
        return null;
    }

    public function getShortTitleModification(CalledContexts $screen_context_stack) : ?ShortTitleModification
    {
        $settings = $this->getArtifact();
        if ($settings->offsetExists(HeaderTitleConfiguration::F_HEADER_TITLE)) {
            $short_title_modification = $this->factory->short_title();
            $short_title_modification = $this->setPriority($short_title_modification);

            return $short_title_modification->withModification(function (string $f) use ($settings) : string {
                return (string) $settings[HeaderTitleConfiguration::F_HEADER_TITLE];
            });
        }
        return null;
    }
}
