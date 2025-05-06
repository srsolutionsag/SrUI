<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\Image\Image;
use ILIAS\GlobalScreen\Scope\Layout\Factory\FooterModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LayoutModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LogoModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\ShortTitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\TitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\UICore\PageContentProvider;
use srag\Plugins\SrUI\UI\PageBuilderStack;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\Menu\MenuColorConfig;
use ILIAS\Filesystem\Stream\Streams;
use srag\Plugins\SrUI\UI\Login\AdvancedFooter;
use ILIAS\GlobalScreen\Identification\PluginIdentificationProvider;
use ILIAS\Data\Meta\Html\UserDefined;

/**
 * Class ilSrUIModificationProvider
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    public const PRIORITY = 60;

    protected PluginIdentificationProvider $if;

    protected function config(): Configuration
    {
        /** @var $this - >plugin ilSrUIPlugin */
        return $this->plugin->getConfiguration();
    }

    public function isInterestedInContexts(): ContextCollection
    {
        return $this->context_collection->main()->main();
    }

    public function getPageBuilderDecorator(CalledContexts $screen_context_stack): ?PageBuilderModification
    {
        $content_settings = $this->config()->contentConfig();
        // Viewport (currently
        if ($content_settings->hasViewPortSetting()) {
            $this->globalScreen()->layout()->meta()->addMetaDatum(new UserDefined('apple-touch-fullscreen', 'yes'));
            $this->globalScreen()->layout()->meta()->addMetaDatum(
                new UserDefined('apple-mobile-web-app-capable', 'yes')
            );
            $this->globalScreen()->layout()->meta()->addMetaDatum(
                new UserDefined('viewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no')
            );
            $this->globalScreen()->layout()->meta()->addOnloadCode('window.scroll(0,1);');
            $this->globalScreen()->layout()->meta()->addMetaDatum(
                new UserDefined('apple-mobile-web-app-status-bar-style', 'black-translucent')
            );
        }

        // Non-Productive Systems
        $prod_system = $this->config()->prodServerConfig();
        // Send X-Robots-Tag: noindex
        if ($prod_system->isNamespaceActive() && !$prod_system->onProdSystem() && $prod_system->sendNonIdexHeader()) {
            $http = $this->dic->http();
            $http->saveResponse(
                $http->response()->withAddedHeader('X-Robots-Tag', 'noindex')
            );
            $http->saveResponse(
                $http->response()->withBody(Streams::ofString(''))
            );
            $http->sendResponse();
        }

        // Cards
        $content_settings = $this->config()->contentConfig();
        if ($content_settings->hasFancyCards() && $this->plugin instanceof ilSrUIPlugin) {
            if ($this->plugin->getILIASVersion()->hasCardFix()) {
                $this->dic->globalScreen()->layout()->meta()->addCss(ilSrUIPlugin::TEMPLATES_BASE . "cards_77.css");
            } else {
                $this->dic->globalScreen()->layout()->meta()->addCss(ilSrUIPlugin::TEMPLATES_BASE . "cards.css");
            }
        }

        // Header-Colors
        $header_colors = $this->config()->headerColorConfig();
        if ($header_colors->isNamespaceActive()) {
            $header_bg_color = $header_colors->getBackgroundColor();
            $bc_bg_color = $header_colors->getBreadcrumbBackgroundColor();
            $bc_text_color = $header_colors->getBreadcrumbTextColor();
            $title_color = $header_colors->getTitleColor();
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
            div.header-inner {
                background-color: $header_bg_color;
            }
            div.breadcrumbs, nav.breadcrumb_wrapper {
                background-color: $bc_bg_color;
                border-color: $bc_bg_color;
            }
            div.breadcrumbs, nav.breadcrumb_wrapper a, span.crumb, span.crumb a {
                color: $bc_text_color !important;
            }
            div.il-pagetitle {
                color: $title_color;
            }
            "
            );
        }

        // Permanent Link
        $footer_permant_link_config = $this->config()->footerPermanentLinkConfig();
        if ($footer_permant_link_config->showEnhancedPermanantLink()) {
            // Standard Colors (Prevent Erroneous Usage of Button Colors)
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                    .il-maincontrols-footer button,
                    .il-maincontrols-footer input,
                    .il-maincontrols-footer img,
                    .il-footer-permanent-url,
                    .il-footer-permanent-url div,
                    .sr-copy-link-container,
                    .sr-copy-link-container span {
                        background-color: white !important;
                        color: #161616 !important;
                        border: none !important;
                    }
                    
                    .il-maincontrols-footer img {
                        filter: none !important;
                    }
                    
                    .il-maincontrols-footer img:hover,
                    .il-maincontrols-footer img:active,
                    .il-maincontrols-footer img:focus{
                        border-bottom: 1px solid #161616 !important;
                    }
                    
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default.engaged),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default.engaged),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:focus-visible:not(.copg-add:focus-visible)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input.enabled),
                    .sr-copy-link-container.btn.btn-default:hover:not(.copg-add:hover),
                    .sr-copy-link-container.btn.btn-default:focus:not(.copg-add:focus),
                    .sr-copy-link-container.btn.btn-default:active:not(.copg-add:active),
                    .sr-copy-link-container.btn.btn-default.enabled,
                    .il-footer-permanent-url:has(> div.sr-copy-link-container:hover:not(.copg-add:hover)) {
                        background-color: white !important;
                        color: #161616 !important;
                        border: none !important;
                    }
                    
                    "
            );
            // Input Width
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                    input.sr-permanent-link-input:hover:not(.copg-add:hover),
                    input.sr-permanent-link-input:active:not(.copg-add:active),
                    input.sr-permanent-link-input:focus:not(.copg-add:focus),
                    input.sr-permanent-link-input:focus-visible:not(.copg-add:focus-visible) {
                        field-sizing: content;
                        width: auto !important;
                    }
                    "
            );
        }

        // Footer Colors
        $footer_colors = $this->config()->footerColorConfig();
        if ($footer_colors->isNamespaceActive()) {
            $footer_bg_color = $footer_colors->getBackgroundColor();
            $footer_text_color = $footer_colors->getTextColor();

            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
            footer, div.il-maincontrols-footer {
                background-color: $footer_bg_color !important;
                color: $footer_text_color;
            }
            
            .il-maincontrols-footer a,
            .il-maincontrols-footer button{
                color: $footer_text_color !important;
            }
           
            "
            );

            // Permanent Link Footer Colors
            if ($footer_permant_link_config->showEnhancedPermanantLink()) {
                $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                    "
                    .il-maincontrols-footer button,
                    .il-maincontrols-footer input,
                    .il-footer-permanent-url,
                    .il-footer-permanent-url div,
                    .sr-copy-link-container,
                    .sr-copy-link-container span {
                        background-color: $footer_bg_color !important;
                        color: $footer_text_color !important;
                        border: none !important;
                    }
                    
                    .il-maincontrols-footer img {
                        filter: invert(100%) !important;
                    }
                    
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-share-trigger.btn.btn-default.engaged),
                    .sr-copy-link-container.btn.btn-default:has(> button.sr-permanent-link-copy-trigger.btn.btn-default.engaged),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:hover:not(.copg-add:hover)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:focus:not(.copg-add:focus)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:focus-visible:not(.copg-add:focus-visible)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input:active:not(.copg-add:active)),
                    .sr-copy-link-container.btn.btn-default:has(> input.sr-permanent-link-input.enabled),
                    .sr-copy-link-container.btn.btn-default:hover:not(.copg-add:hover),
                    .sr-copy-link-container.btn.btn-default:focus:not(.copg-add:focus),
                    .sr-copy-link-container.btn.btn-default:active:not(.copg-add:active),
                    .sr-copy-link-container.btn.btn-default.enabled,
                    .sr-permanent-link-share-trigger.btn.btn-default:hover:not(.copg-add:hover),
                    .sr-permanent-link-share-trigger.btn.btn-default:active:not(.copg-add:active),
                    .sr-permanent-link-share-trigger.btn.btn-default:focus:not(.copg-add:focus),
                    .sr-permanent-link-copy-trigger.btn.btn-default:hover:not(.copg-add:hover),
                    .sr-permanent-link-copy-trigger.btn.btn-default:active:not(.copg-add:active),
                    .sr-permanent-link-copy-trigger.btn.btn-default:focus:not(.copg-add:focus),
                    .il-footer-permanent-url:has(> div.sr-copy-link-container:hover:not(.copg-add:hover)) {
                        background-color: $footer_bg_color !important;
                        color: $footer_text_color !important;
                        border: none !important;
                    }
                    
                    "
                );
            }
        }


        // Menu Colors
        $menu_colors = $this->config()->menuColorConfig();
        if ($menu_colors->isNamespaceActive()) {
            $main_color = $menu_colors->get(MenuColorConfig::F_MAIN_COLOR);
            $main_hover_color = $menu_colors->get(MenuColorConfig::F_MAIN_HOVER_COLOR);
            $sub_color = $menu_colors->get(MenuColorConfig::F_SUB_COLOR);
            $sub_hover_color = $menu_colors->get(MenuColorConfig::F_SUB_HOVER_COLOR);
            $tools_color = $menu_colors->get(MenuColorConfig::F_TOOLS_COLOR);
            $tools_hover_color = $menu_colors->get(MenuColorConfig::F_TOOLS_HOVER_COLOR);

            // Icon Color
            $filter_white = ' brightness(0) saturate(100%) invert(100%) sepia(99%) saturate(0%) hue-rotate(313deg) brightness(104%) contrast(100%)';
            $filter_black = ' brightness(0) saturate(100%)';
            
            [$color_standard, $color_engaged] = explode('/', $menu_colors->getIconColor());
            
            $filter_standard = match ($color_standard) {
                'white' => $filter_white,
                'black' => $filter_black,
            };
            
            $filter_engaged = match ($color_engaged) {
                'white' => $filter_white,
                'black' => $filter_black,
            };
            
            $this->dic->globalScreen()->layout()->meta()->addInlineCss("
                .il-mainbar-triggers .btn-bulky .icon, .il-mainbar-triggers .il-link.link-bulky .icon {
                    filter: $filter_standard !important;
                }
                .il-mainbar-triggers .btn-bulky:hover .icon,
                .il-mainbar-triggers .il-link.link-bulky:hover .icon,
                .il-mainbar-triggers .btn-bulky.engaged .icon,
                .il-mainbar-triggers .il-link.link-bulky.engaged .icon {
                    filter: $filter_engaged !important;
                }
                .il-mainbar-triggers .bulky-label {
                    color: $color_standard !important;
                }
                
                .il-mainbar-triggers .btn-bulky:hover .bulky-label,
                .il-mainbar-triggers .il-link.link-bulky:hover .bulky-label,
                .il-mainbar-triggers .btn-bulky.engaged .bulky-label,
                .il-mainbar-triggers .il-link.link-bulky.engaged .bulky-label {
                    color: $color_engaged !important;
                }
            ");

            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
            nav.il-mainbar, div.il-mainbar-triggers, .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .il-link.link-bulky {
                background-color: $main_color !important;
                color: $main_hover_color !important;
            }
 
            .il-mainbar-triggers .btn-bulky:hover, .il-mainbar-triggers .il-link.link-bulky:hover,
            .il-mainbar-triggers .btn-bulky.engaged, .il-mainbar-triggers .il-link.link-bulky.engaged {
                background-color: $main_hover_color !important;
                color: $main_color !important;
            }

            .il-maincontrols-slate .btn-bulky, .il-maincontrols-slate .link-bulky,
            .il-drilldown .menulevel {
                background-color: $sub_color !important;
            }
            
            .il-maincontrols-slate .btn-bulky:hover, .il-maincontrols-slate .link-bulky:hover,
            .il-maincontrols-slate .btn-bulky.engaged, .il-maincontrols-slate .link-bulky.engaged,
            .il-drilldown .menulevel:hover {
                background-color: $sub_hover_color !important;
            }
           
            .il-maincontrols-slate.engaged[data-depth-level=\"2\"] .btn-bulky,
            .il-maincontrols-slate.engaged[data-depth-level=\"2\"] .link-bulky {
                padding-left: 35px;
            }
         
            .il-mainbar-tools-button .btn-bulky {
                background-color: $tools_color !important;
            }
            .il-mainbar-tools-button .btn-bulky.engaged {
                background-color: $tools_hover_color !important;
            }
            "
            );
        }

        // Button Colors
        $button_colors = $this->config()->buttonColorConfig();
        if ($button_colors->isNamespaceActive()) {
            $primary = $button_colors->getPrimaryColor();
            $primary_text_color = $button_colors->getPrimaryTextColor();
            $primary_hover = $button_colors->getPrimaryHoverColor();
            $primary_hover_text_color = $button_colors->getPrimaryHoverTextColor();

            $general = $button_colors->getGeneralColor();
            $general_text_color = $button_colors->getGeneralTextColor();
            $general_hover = $button_colors->getGeneralHoverColor();
            $general_hover_text_color = $button_colors->getGeneralHoverTextColor();

            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                .btn.btn-primary {
                    background-color: $primary !important;
                    border-color: $primary_text_color !important;
                    color: $primary_text_color !important;
                }
                .btn.btn-primary:focus, .btn.btn-primary:hover, .btn.btn-primary:active {
                    background-color: $primary_hover !important;
                    border-color: $primary_hover_text_color !important;
                    color: $primary_hover_text_color !important;
                }
                .btn.btn-default:not(.copg-add):not([data-copg-ed-type=\"par-action\"])
                 {
                    background-color: $general;
                    border-color: $general_text_color;
                    color: $general_text_color;
                }
                .btn.btn-default:focus:not(.copg-add:focus),
                .btn.btn-default:hover:not(.copg-add:hover),
                .btn.btn-default:active:not(.copg-add:active),
                .btn.btn-default.engaged
                {
                    background-color: $general_hover !important;
                    border-color: $general_hover_text_color !important;
                    color: $general_hover_text_color !important;
                }
            "
            );
        }

        // Tabs
        $tabs_colors = $this->config()->contentConfig()->getTabLinkColor();
        if(!empty($tabs_colors)) {
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                ul#ilTab li a {
                    color: $tabs_colors;
                }
            "
            );
        }

        // Object Header
        $object_header_color = $this->config()->contentConfig()->getObjectHeaderColor();
        if(!empty($object_header_color)) {
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                h1.il-page-content-header {
                    color: $object_header_color !important;
                }
            "
            );
        }

        if($this->config()->contentConfig()->hasBlackHeaderIcon()) {
            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                img#headerimage {
                    filter: $filter_black !important;
                }
            "
            );
        }


        // Page
        $builder = new PageBuilderStack($this->dic, $this->config());
        return $builder->getAsModification();
    }

    private function setPriority(LayoutModification $m): LayoutModification
    {
        try {
            return $m->withPriority(self::PRIORITY);
        } catch (Throwable) {
            return $m->withHighPriority();
        }
    }

    public function getLogoModification(CalledContexts $screen_context_stack): ?LogoModification
    {
        $header_logo = $this->config()->headerLogoConfig();
        $header_logo_link = $header_logo->getHeaderIconLink();
        $logo_modification = $this->factory->logo();
        $logo_modification = $this->setPriority($logo_modification);

        if ($header_logo->hasHeaderLogo()) {
            $width = $header_logo->getHeaderLogoWidth();
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo .hidden-xs img { width: {$width};}");
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo img.img-standard { width: {$width};}");
            $s = $this->plugin->getStorageFactory()->storage()->getDataURL($header_logo->getHeaderlogo());
            if (!empty($s)) {
                return $logo_modification->withModification(
                    function (?Image $image) use ($s, $header_logo_link): ?Image {
                        if ($image === null) {
                            return null;
                        }
                        return $image->withSource($s)
                                     ->withAction($header_logo_link === '' ? $image->getAction() : $header_logo_link);
                    }
                );
            }
        }

        return $logo_modification->withModification(
            function (?Image $image) use (
                $header_logo_link
            ): ?Image {
                if ($image === null) {
                    return null;
                }
                return $image->withAction($header_logo_link === '' ? $image->getAction() : $header_logo_link);
            }
        );
    }

    public function getResponsiveLogoModification(CalledContexts $screen_context_stack): ?LogoModification
    {
        $responsive_header_logo = $this->config()->responsiveHeaderLogoConfig();
        if ($responsive_header_logo->hasResponsiveHeaderLogo()) {
            $width = $responsive_header_logo->getResponsiveHeaderLogoWidth();
            $this->globalScreen()->layout()->meta()->addInlineCss(
                "
            @media (max-width: 767px) {
                .il-logo, .il-logo img.img-standard {
                    width: {$width} !important;
                }
            }
            "
            );
            $s = $this->plugin->getStorageFactory()->storage()->getDataURL(
                $responsive_header_logo->getResponsiveHeaderLogo()
            );
            if (!empty($s)) {
                $logo_modification = $this->factory->logo();
                $logo_modification = $this->setPriority($logo_modification);

                return $logo_modification->withModification(
                    function (?Image $image) use ($s): ?Image {
                        if ($image === null) {
                            return null;
                        }
                        return $image->withSource($s);
                    }
                );
            }
        }
        return null;
    }

    public function getFooterModification(CalledContexts $screen_context_stack): ?FooterModification
    {
        $footer_modification = $this->factory->footer();
        $footer_modification = $this->setPriority($footer_modification);
        $page_content_provider = new PageContentProvider($this->dic);
        //$screen_context_stack = $this->dic->globalScreen()->tool()->context()->stack();
        $footer_additional_infos_config = $this->config()->footerAdditionalInfosConfig();
        $footer_permant_link_config = $this->config()->footerPermanentLinkConfig();

        // ILIAS 8 css fix
        $this->globalScreen()->layout()->meta()->addInlineCss(
            "
            footer {
                height: auto !important;
            }
            
            @media only screen and (max-width: 768px) {
                footer {
                    margin-bottom: 0px !important;
                }
            }
            "
        );

        $modification = function (?Footer $f) use (
            $page_content_provider,
            $screen_context_stack,
            $footer_additional_infos_config,
            $footer_permant_link_config
        ): ?Footer {
            if ($f === null) {
                return null;
            }

            // BUILD original Footer
            /** @var $f Footer */
            $f = $page_content_provider->getFooterModification($screen_context_stack)->getModification()(null);

            // Hostname
            $footer_text = $f->getText();
            if ($footer_additional_infos_config->showRenderedBy()) {
                $hostname = gethostname();
                $ilias_version = ILIAS_VERSION_NUMERIC;
                preg_match('/\d+\.\d+.\d+/', PHP_VERSION, $php_version);
                $footer_text = "Rendered by " . $hostname . " - " . $ilias_version . " - " . $php_version[0];
            }

            if ($footer_permant_link_config->showEnhancedPermanantLink()) {
                $footer = new AdvancedFooter(
                    $f->getLinks(),
                    $footer_text
                );
            } else {
                $footer = $this->dic->ui()->factory()->mainControls()->footer(
                    $f->getLinks(),
                    $footer_text
                );
            }
            if ($f->getPermanentURL() !== null) {
                return $footer->withPermanentURL($f->getPermanentURL());
            }

            return $footer;
        };

        return $footer_modification->withModification($modification);

        if ($footer_additional_infos_config->showRenderedBy()) {
            $footer_modification = $this->factory->footer();
            $footer_modification = $this->setPriority($footer_modification);

            return $footer_modification
                ->withModification(
                    function (?Footer $f): ?Footer {
                        if ($f === null) {
                            return null;
                        }
                        $pageContentProvider = new PageContentProvider($this->dic);
                        $footer = $pageContentProvider->getFooterModification(
                            $this->dic->globalScreen()->tool()->context()->stack()
                        )->getModification();
                        /**
                         * @var $f Footer
                         */
                        $f = $footer(null);

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
                            return $replaced_footer->withPermanentURL($f->getPermanentURL());
                        }
                        return $replaced_footer;
                    }
                );
        }
        return null;
    }

    public function getTitleModification(CalledContexts $screen_context_stack): ?TitleModification
    {
        $title_config = $this->config()->titleConfig();
        if ($title_config->isNamespaceActive()) {
            $title_modification = $this->factory->title();
            $title_modification = $this->setPriority($title_modification);

            return $title_modification->withModification(fn(?string $f): ?string => $title_config->getHeaderTitle());
        }
        return null;
    }

    public function getShortTitleModification(CalledContexts $screen_context_stack): ?ShortTitleModification
    {
        $title_config = $this->config()->titleConfig();
        if ($title_config->isNamespaceActive()) {
            $short_title_modification = $this->factory->short_title();
            $short_title_modification = $this->setPriority($short_title_modification);

            return $short_title_modification->withModification(
                fn(?string $f): ?string => $title_config->getHeaderTitle()
            );
        }
        return null;
    }
}
