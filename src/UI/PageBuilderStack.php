<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\UI;

use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\Scope\Layout\Factory\ModificationFactory;
use ILIAS\GlobalScreen\Scope\Layout\MetaContent\MetaContent;
use ILIAS\GlobalScreen\Scope\Layout\Builder\StandardPageBuilder;
use ILIAS\UI\Component\Layout\Page\Page;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\GlobalScreen\Scope\Tool\Factory\ToolFactory;
use ILIAS\DI\Container;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use srag\Plugins\SrUI\UI\Login\LoginPageSettings;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\UI\Login\Login;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\Config\Configuration;
use ILIAS\Container\Screen\MemberViewLayoutProvider;
use ILIAS\UI\Implementation\Component\Layout\Page\Standard;

/**
 * Class PageBuilderStack
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class PageBuilderStack extends StandardPageBuilder
{
    /**
     * @var ContextCollection
     */
    protected CalledContexts $context_stack;
    /**
     * @var ToolFactory
     */
    protected ModificationFactory $factory;
    /**
     * @var MetaContent
     */
    protected MetaContent $meta;
    protected Factory $storage_factory;

    /**
     * @inheritDoc
     */
    public function __construct(protected Container $dic, protected Configuration $configuration)
    {
        parent::__construct();
        $this->context_stack = $this->dic->globalScreen()->tool()->context()->stack();
        $this->factory = $this->dic->globalScreen()->layout()->factory();
        $this->meta = $this->dic->globalScreen()->layout()->meta();
        $this->storage_factory = new Factory();
    }

    public function build(PagePartProvider $parts): Page
    {
        $parts = new PagePartProviderStack($parts);

        if ($this->context_stack->current()->getAdditionalData()->is(\ilSrUIPlugin::ON_LOGIN_PAGE, true)) {
            // SHOW NO PROD WARNING
            if (
                $this->configuration->prodServerConfig()->isNamespaceActive()
                && !$this->configuration->prodServerConfig()->onProdSystem()
            ) {
                $text = $this->ui->factory()->modal()->lightboxTextPage(
                    $this->configuration->prodServerConfig()->getNonProdMessage(),
                    $this->configuration->prodServerConfig()->getNonProdMessageTitle()
                );
                $modal = $this->ui->factory()->modal()->lightbox([$text]);

                $show_id = $modal->getShowSignal()->getId();
                $close_id = $modal->getCloseSignal()->getId();

                $modal = $modal->withAdditionalOnLoadCode(function ($id) {
                    return "$('#$id').on('hidden.bs.modal', function () {
                        document.cookie = 'sr_ui_prod_warning=1; path=/';
                    });";
                });

                $parts->prependContent($this->ui->renderer()->render($modal));

                $this->meta->addOnloadCode("
                if (document.cookie.replace(/(?:(?:^|.*;\s*)sr_ui_prod_warning\s*\=\s*([^;]*).*$)|^.*$/, '$1') !== '1') {
                    $(document).trigger('$show_id', '{}');
                }");

            }

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            $login = $this->getLoginPage($parts);
            foreach ($this->meta->getMetaData() as $meta_datum) {
                $login = $login->withAdditionalMetaDatum($meta_datum);
            }

            if (null !== ($og_meta_data = $this->meta->getOpenGraphMetaData())) {
                $login = $login->withAdditionalMetaDatum($og_meta_data);
            }

            return $login->withSystemInfos($parts->getSystemInfos())
                         ->withTextDirection($this->meta->getTextDirection() ?? Standard::LTR);
        }

        // Fix for ModeInfo/MemberView
        $page = parent::build($parts);
        $mv_mode_info = MemberViewLayoutProvider::getMemberViewModeInfo($this->dic);
        if (!is_null($mv_mode_info)) {
            $page = $page->withModeInfo($mv_mode_info);
        }

        foreach ($this->meta->getMetaData() as $meta_datum) {
            $page = $page->withAdditionalMetaDatum($meta_datum);
        }

        if (null !== ($og_meta_data = $this->meta->getOpenGraphMetaData())) {
            return $page->withAdditionalMetaDatum($og_meta_data);
        }

        return $page;
    }

    public function getAsModification(): ?PageBuilderModification
    {
        return $this->factory->page()->withPriority(16)->withModification(
            fn(PagePartProvider $p): Page => $this->build($p)
        );
    }

    /**
     * @param PagePartProvider $parts
     * @return Login
     */
    private function getLoginPage(PagePartProvider $parts): Login
    {
        $centered_page = $this->configuration->get(CenteredPageConfig::NS);
        $show_components = $this->configuration->get(ShowComponentsConfig::NS);

        $login_page_settings = new LoginPageSettings();
        $login_page_settings->setAdditionalHtml($centered_page->get(CenteredPageConfig::F_ADDITIONAL_HTML) ?? '');
        $login_page_settings->setCenterLogin($centered_page->isNamespaceActive());

        if ($login_page_settings->isCenterLogin()) {
            $this->meta->addInlineCss($centered_page->getLoginCSS());
        }
        $login_page_settings->setShowHeader((bool) ($show_components->get(ShowComponentsConfig::F_SHOW_HEADER, true)));
        $login_page_settings->setShowFooter((bool) ($show_components->get(ShowComponentsConfig::F_SHOW_FOOTER, true)));
        $login_page_settings->setShowMainBar(
            (bool) ($show_components->get(
                ShowComponentsConfig::F_SHOW_MAIN_BAR,
                true
            ))
        );
        $login_page_settings->setShowHeaderLogo(
            (bool) ($centered_page->get(
                CenteredPageConfig::F_SHOW_HEADER_LOGO,
                true
            ))
        );

        if ($centered_page->get(CenteredPageConfig::F_BACKGROUND_IMAGE)) {
            $s = $this->storage_factory->storage()->getDataURL(
                (string) $centered_page->get(CenteredPageConfig::F_BACKGROUND_IMAGE)[0]
            );
            if (!empty($s)) {
                $login_page_settings->setBackgroundImage($s);
            }
        }

        if (!$login_page_settings->isShowHeader()) {
            //            $this->meta->addCss(\ilSrUIPlugin::TEMPLATES_BASE . 'no_header.css');
        }

        if (!$login_page_settings->isShowHeader() && !$login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 100vh;}");
        } elseif (!$login_page_settings->isShowHeader() && $login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 94.5vh;}");
        } elseif ($login_page_settings->isShowHeader() && !$login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 90.5vh;}");
        } else {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 91.5vh;}");
        }

        return new Login(
            $login_page_settings,
            [$parts->getContent()],
            $login_page_settings->isShowHeader() ? $parts->getMetaBar() : null,
            $login_page_settings->isShowMainBar() ? $parts->getMainBar() : null,
            $parts->getBreadCrumbs(),
            $parts->getLogo(),
            $parts->getResponsiveLogo(),
            $parts->getFaviconPath(),
            $parts->getToastContainer(),
            $parts->getFooter(),
            $parts->getTitle(),
            $parts->getShortTitle(),
            $parts->getViewTitle()
        );
    }
}
