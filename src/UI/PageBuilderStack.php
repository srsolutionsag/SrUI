<?php

namespace srag\Plugins\SrUI\UI;

use ILIAS\GlobalScreen\Scope\Layout\Builder\StandardPageBuilder;
use ILIAS\UI\Component\Layout\Page\Page;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\GlobalScreen\Scope\Tool\Factory\ToolFactory;
use ILIAS\DI\Container;
use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use srag\Plugins\SrUI\Artifact\Artifact;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\LoginPageSettings;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Login;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;

/**
 * Class PageBuilderStack
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class PageBuilderStack extends StandardPageBuilder
{
    /**
     * @var ContextCollection
     */
    protected $context_stack;
    /**
     * @var ToolFactory
     */
    protected $factory;
    /**
     * @var Artifact
     */
    protected $settings;
    /**
     * @var \ILIAS\GlobalScreen\Scope\Layout\MetaContent\MetaContent
     */
    protected $meta;

    /**
     * @inheritDoc
     */
    public function __construct(Container $dic, Artifact $settings)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->context_stack = $dic->globalScreen()->tool()->context()->stack();
        $this->factory = $dic->globalScreen()->layout()->factory();
        $this->meta = $dic->globalScreen()->layout()->meta();
    }

    public function build(PagePartProvider $parts) : Page
    {
        //$parts->getContent()
        $parts = new \srag\Plugins\SrUI\UI\PagePartProviderStack($parts);


        if ($this->context_stack->current()->getAdditionalData()->is(\ilSrUIPlugin::ON_LOGIN_PAGE, true)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this->getLoginPage($parts);
        }

        return parent::build($parts);
    }

    public function getAsModification() : ?PageBuilderModification
    {
        return $this->factory->page()->withLowPriority()->withModification(function (PagePartProvider $p) : Page {
            return $this->build($p);
        });
    }

    /**
     * @param PagePartProvider $parts
     * @return Login
     */
    private function getLoginPage(PagePartProvider $parts) : Login
    {
        $login_page_settings = new LoginPageSettings();
        $login_page_settings->setCenterLogin((bool) $this->settings[CenteredPageConfig::NS]);

        if ($login_page_settings->isCenterLogin()) {
            $this->meta->addInlineCss((string) $this->settings[CenteredPageConfig::F_CENTERED_LOGIN_PAGE_CSS]);
        }
        $login_page_settings->setShowHeader((bool) ($this->settings[ShowComponentsConfig::F_SHOW_HEADER] ?? true));
        $login_page_settings->setShowFooter((bool) ($this->settings[ShowComponentsConfig::F_SHOW_FOOTER] ?? true));
        $login_page_settings->setShowMainBar((bool) ($this->settings[ShowComponentsConfig::F_SHOW_MAIN_BAR] ?? true));
        $login_page_settings->setShowHeaderLogo((bool) ($this->settings[CenteredPageConfig::F_SHOW_HEADER_LOGO] ?? true));

        if ($this->settings[CenteredPageConfig::F_BACKGROUND_IMAGE]) {
            $s = Factory::storage()->getDataURL((string) $this->settings[CenteredPageConfig::F_BACKGROUND_IMAGE][0]);
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
