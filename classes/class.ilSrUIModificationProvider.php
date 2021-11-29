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

/**
 * Class ilSrUIModificationProvider
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
            if (!in_array(gethostname(), $prod_system->getHostnames())) {
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
            div.il-pagetitle {
                color: $title_color;
            }
            ");
        }

        // Footer Colors
        $footer_colors = $this->config()->footerColorConfig();
        if ($footer_colors->isNamespaceActive()) {
            $footer_bg_color = $footer_colors->getBackgroundColor();
            $footer_text_color = $footer_colors->getTextColor();

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
        if ($header_logo->hasHeaderLogo()) {
            $with = $header_logo->getHeaderLogoWidth();
            $this->globalScreen()->layout()->meta()->addInlineCss(".il-logo img { width: {$with};}");
            $s = Factory::storage()->getDataURL($header_logo->getHeaderlogo());
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
        $footer_additional_infos_config = $this->config()->footerAdditionalInfosConfig();
        if ($footer_additional_infos_config->showRenderedBy()) {

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
