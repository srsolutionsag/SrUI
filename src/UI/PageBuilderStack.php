<?php

namespace srag\Plugins\SrUI\UI;

use ILIAS\GlobalScreen\Scope\Layout\Builder\StandardPageBuilder;
use ILIAS\UI\Component\Layout\Page\Page;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\GlobalScreen\Scope\Tool\Factory\ToolFactory;
use ILIAS\DI\Container;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\LoginPageSettings;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Login;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\Config\Configuration;
use ILIAS\Container\Screen\MemberViewLayoutProvider;

/**
 * Class PageBuilderStack
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class PageBuilderStack extends StandardPageBuilder
{
    /**
     * @var ContextCollection
     */
    protected \ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts $context_stack;
    /**
     * @var ToolFactory
     */
    protected \ILIAS\GlobalScreen\Scope\Layout\Factory\ModificationFactory $factory;
    protected \srag\Plugins\SrUI\Config\Configuration $configuration;
    /**
     * @var \ILIAS\GlobalScreen\Scope\Layout\MetaContent\MetaContent
     */
    protected \ILIAS\GlobalScreen\Scope\Layout\MetaContent\MetaContent $meta;
    protected \srag\Plugins\SrUI\ResourceStorage\Factory $storage_factory;
    protected \ILIAS\DI\Container $dic;

    /**
     * @inheritDoc
     */
    public function __construct(Container $dic, Configuration $settings)
    {
        parent::__construct();
        $this->dic = $dic;
        $this->configuration = $settings;
        $this->context_stack = $dic->globalScreen()->tool()->context()->stack();
        $this->factory = $dic->globalScreen()->layout()->factory();
        $this->meta = $dic->globalScreen()->layout()->meta();
        $this->storage_factory = new Factory();
    }

    public function build(PagePartProvider $parts): Page
    {
        $parts = new \srag\Plugins\SrUI\UI\PagePartProviderStack($parts);

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
                $parts->prependContent($this->ui->renderer()->render($modal));
                $id = $modal->getShowSignal()->getId();
                $this->meta->addOnloadCode("$(document).trigger('$id', '{}');");
            }

            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->getLoginPage($parts);
        }

        // Fix for ModeInfo/MemberView
        $page = parent::build($parts);
        $mv_mode_info = MemberViewLayoutProvider::getMemberViewModeInfo($this->dic);
        if (!is_null($mv_mode_info)) {
            $page = $page->withModeInfo($mv_mode_info);
        }

        return $page;
    }

    public function getAsModification(): ?PageBuilderModification
    {
        return $this->factory->page()->withPriority(16)->withModification(function (PagePartProvider $p): Page {
            return $this->build($p);
        });
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
        $login_page_settings->setCenterLogin($centered_page->isNamespaceActive());

        if ($login_page_settings->isCenterLogin()) {
            $this->meta->addInlineCss($centered_page->getLoginCSS());
        }
        $login_page_settings->setShowHeader((bool) ($show_components->get(ShowComponentsConfig::F_SHOW_HEADER, true)));
        $login_page_settings->setShowFooter((bool) ($show_components->get(ShowComponentsConfig::F_SHOW_FOOTER, true)));
        $login_page_settings->setShowMainBar((bool) ($show_components->get(
            ShowComponentsConfig::F_SHOW_MAIN_BAR,
            true
        )));
        $login_page_settings->setShowHeaderLogo((bool) ($centered_page->get(
            CenteredPageConfig::F_SHOW_HEADER_LOGO,
            true
        )));

        if ($centered_page->get(CenteredPageConfig::F_BACKGROUND_IMAGE)) {
            $s = $this->storage_factory->storage()->getDataURL((string) $centered_page->get(CenteredPageConfig::F_BACKGROUND_IMAGE)[0]);
            if (!empty($s)) {
                $login_page_settings->setBackgroundImage($s);
            }
        }

        if (!$login_page_settings->isShowHeader()) {
            $this->meta->addCss(\ilSrUIPlugin::TEMPLATES_BASE . 'no_header.css');
        }

        if (!$login_page_settings->isShowHeader() && !$login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 100vh;}");
        } elseif (!$login_page_settings->isShowHeader() && $login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 94.5vh;}");
        } elseif ($login_page_settings->isShowHeader() && !$login_page_settings->isShowFooter()) {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 90.5vh;}");
        } else {
            $this->meta->addInlineCss(".sr-ui-content {min-height: 85vh;}");
        }

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
    }
}
