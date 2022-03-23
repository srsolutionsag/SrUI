<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

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

/**
 * Class ilSrUIModificationProvider
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIModificationProvider extends AbstractModificationPluginProvider
{
    const PRIORITY = 60;
    
    protected function config() : Configuration
    {
        /** @var $this- >plugin ilSrUIPlugin */
        return $this->plugin->getConfiguration();
    }
    
    public function isInterestedInContexts() : ContextCollection
    {
        return $this->context_collection->main()->main();
    }
    
    public function getPageBuilderDecorator(CalledContexts $screen_context_stack) : ?PageBuilderModification
    {
        // Non-Productive Systems
        $prod_system = $this->config()->prodServerConfig();
        if ($prod_system->isNamespaceActive()) {
            if (!$prod_system->onProdSystem()) {
                // Send X-Robots-Tag: noindex
                if ($prod_system->sendNonIdexHeader()) {
                    $repsonse = $this->dic->http()->response()->withAddedHeader('X-Robots-Tag', 'noindex');
                    $this->dic->http()->saveResponse($repsonse);
                    $this->dic->http()->sendResponse();
                }
            }
        }
        
        // Cards
        $content_settings = $this->config()->contentConfig();
        if ($content_settings->hasFancyCards()) {
            $this->dic->globalScreen()->layout()->meta()->addCss(ilSrUIPlugin::TEMPLATES_BASE . "cards.css");
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
            div.breadcrumbs, nav.breadcrumb_wrapper a {
                color: $bc_text_color;
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
            
            
            $this->dic->globalScreen()->layout()->meta()->addInlineCss("
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
                .btn.btn-default {
                    background-color: $general;
                    border-color: $general_text_color;
                    color: $general_text_color;
                }
                .btn.btn-default:focus, .btn.btn-default:hover, .btn.btn-default:active {
                    background-color: $general_hover !important;
                    border-color: $general_hover_text_color !important;
                    color: $general_hover_text_color !important;
                }
            ");
        }

        // Page
        $builder = new PageBuilderStack($this->dic, $this->config());
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
        $header_logo = $this->config()->headerLogoConfig();
        $header_logo_link = $header_logo->getHeaderIconLink();
        $logo_modification = $this->factory->logo();
        $logo_modification = $this->setPriority($logo_modification);
        
        if ($header_logo->hasHeaderLogo()) {
            $with = $header_logo->getHeaderLogoWidth();
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo img { width: {$with};}");
            $s = Factory::storage()->getDataURL($header_logo->getHeaderlogo());
            if (!empty($s)) {
                return $logo_modification->withModification(function (Image $image) use ($s, $header_logo_link) {
                    return $image->withSource($s)
                                 ->withAction($header_logo_link === '' ? $image->getAction() : $header_logo_link);
                });
            }
        }
        
        return $logo_modification->withModification(function (Image $image) use ($s, $header_logo_link) {
            return $image->withAction($header_logo_link === '' ? $image->getAction() : $header_logo_link);
        });
    }
    
    public function getFooterModification(CalledContexts $screen_context_stack) : ?FooterModification
    {
        $footer_additional_infos_config = $this->config()->footerAdditionalInfosConfig();
        if ($footer_additional_infos_config->showRenderedBy()) {
            
            $footer_modification = $this->factory->footer();
            $footer_modification = $this->setPriority($footer_modification);
            
            return $footer_modification
                ->withModification(function () : \ILIAS\UI\Component\MainControls\Footer {
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
                });
        }
        return null;
    }
    
    public function getTitleModification(CalledContexts $screen_context_stack) : ?TitleModification
    {
        $title_config = $this->config()->titleConfig();
        if ($title_config->isNamespaceActive()) {
            $title_modification = $this->factory->title();
            $title_modification = $this->setPriority($title_modification);
            
            return $title_modification->withModification(function (string $f) use ($title_config) : string {
                return $title_config->getHeaderTitle();
            });
        }
        return null;
    }
    
    public function getShortTitleModification(CalledContexts $screen_context_stack) : ?ShortTitleModification
    {
        $title_config = $this->config()->titleConfig();
        if ($title_config->isNamespaceActive()) {
            $short_title_modification = $this->factory->short_title();
            $short_title_modification = $this->setPriority($short_title_modification);
            
            return $short_title_modification->withModification(function (string $f) use ($title_config) : string {
                return $title_config->getHeaderTitle();
            });
        }
        return null;
    }
}
