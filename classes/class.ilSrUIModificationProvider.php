<?php

/** @noRector  */
require_once(__DIR__ . '/../vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\Layout\Factory\FooterModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LayoutModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LogoModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\ShortTitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Factory\TitleModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\UI\Implementation\Component\Image\Image;
use ILIAS\UI\Implementation\Component\MainControls\Footer;
use ILIAS\UICore\PageContentProvider;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\UI\PageBuilderStack;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\Menu\MenuColorConfig;
use ILIAS\Filesystem\Stream\Streams;

/**
 * Class ilSrUIModificationProvider
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    public const PRIORITY = 60;

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
        // Non-Productive Systems
        $prod_system = $this->config()->prodServerConfig();
        if ($prod_system->isNamespaceActive() && !$prod_system->onProdSystem()) {
            // Send X-Robots-Tag: noindex
            if ($prod_system->sendNonIdexHeader()) {
                $http = $this->dic->http();

                $http->saveResponse(
                    $http->response()->withAddedHeader('X-Robots-Tag', 'noindex')
                );
                $http->saveResponse(
                    $http->response()->withBody(Streams::ofString(''))
                );
                $http->sendResponse();
            }
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
            
            .il-maincontrols-footer .il-footer-links li a {
                color: $footer_text_color !important;
            }
           
            "
            );
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

            $this->dic->globalScreen()->layout()->meta()->addInlineCss(
                "
                
            nav.il-mainbar, div.il-mainbar-triggers, .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .il-link.link-bulky {
                background-color: $main_color !important;
                color: $main_hover_color !important;
            }
            // Round Menu (disbaled, needs setting)
            .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .btn-bulky, .il-mainbar-triggers .il-link.link-bulky {
//                border-radius: 50%;
//                border: 0px solid $main_hover_color !important;
//                margin-bottom: 3px
            }
 
            .il-mainbar-triggers .btn-bulky:hover, .il-mainbar-triggers .il-link.link-bulky:hover,
            .il-mainbar-triggers .btn-bulky.engaged, .il-mainbar-triggers .il-link.link-bulky.engaged {
                background-color: $main_hover_color !important;
                color: $main_color !important;
            }

            .il-maincontrols-slate .btn-bulky, .il-maincontrols-slate .link-bulky {
                background-color: $sub_color !important;
            }
            
            .il-maincontrols-slate .btn-bulky:hover, .il-maincontrols-slate .link-bulky:hover,
            .il-maincontrols-slate .btn-bulky.engaged, .il-maincontrols-slate .link-bulky.engaged {
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
                .btn.btn-default:not(.copg-add) {
                    background-color: $general;
                    border-color: $general_text_color;
                    color: $general_text_color;
                }
                .btn.btn-default:focus:not(.copg-add:focus), .btn.btn-default:hover:not(.copg-add:hover), .btn.btn-default:active:not(.copg-add:active) {
                    background-color: $general_hover !important;
                    border-color: $general_hover_text_color !important;
                    color: $general_hover_text_color !important;
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
        } catch (Throwable $t) {
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
                    function (?\ILIAS\UI\Component\Image\Image $image) use (
                        $s,
                        $header_logo_link
                    ) : ?\ILIAS\UI\Component\Image\Image {
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
            function (?\ILIAS\UI\Component\Image\Image $image) use ($header_logo_link
            ) : ?\ILIAS\UI\Component\Image\Image {
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
                    function (?\ILIAS\UI\Component\Image\Image $image) use ($s) : ?\ILIAS\UI\Component\Image\Image {
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
        $footer_additional_infos_config = $this->config()->footerAdditionalInfosConfig();
        if ($footer_additional_infos_config->showRenderedBy()) {
            $footer_modification = $this->factory->footer();
            $footer_modification = $this->setPriority($footer_modification);

            return $footer_modification
                ->withModification(
                    function (?\ILIAS\UI\Component\MainControls\Footer $f) : ?\ILIAS\UI\Component\MainControls\Footer {
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
            
            return $title_modification->withModification(function (?string $f) use ($title_config) : ?string {
                return $title_config->getHeaderTitle();
            });
        }
        return null;
    }

    public function getShortTitleModification(CalledContexts $screen_context_stack): ?ShortTitleModification
    {
        $title_config = $this->config()->titleConfig();
        if ($title_config->isNamespaceActive()) {
            $short_title_modification = $this->factory->short_title();
            $short_title_modification = $this->setPriority($short_title_modification);
            
            return $short_title_modification->withModification(function (?string $f) use ($title_config) : ?string {
                return $title_config->getHeaderTitle();
            });
        }
        return null;
    }
}
