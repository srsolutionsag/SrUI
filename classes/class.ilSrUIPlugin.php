<?php

/** @noRector  */
require_once(__DIR__ . '/../vendor/autoload.php');

use ILIAS\DI\Container;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\Content\Config;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\ILIASVersion;
use srag\Plugins\SrUI\Config\Migration\MigrationCollection;
use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Migration\To1_6_0Migration;
use srag\Plugins\SrUI\Config\Migration\To1_7_0Migration;
use ILIAS\FileUpload\MimeType;
use srag\Plugins\SrUI\Config\Header\ResponsiveHeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Content\SpecialContentFeaturesConfig;

/**
 * Class ilSrUIPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIPlugin extends ilUserInterfaceHookPlugin
{
    public const PLUGIN_BASE = __DIR__ . '/../';
    public const TEMPLATES_BASE = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/";
    public const OUTSIDE_ILIAS = 'anon';
    public const ON_LOGIN_PAGE = 'on_login_page';

    protected \srag\Plugins\SrUI\Config\Configuration $configuration;
    protected ?ILIASVersion $ilias_version = null;
    protected \srag\Plugins\SrUI\ResourceStorage\Factory $storage_factory;

    public function getPluginName(): string
    {
        return 'SrUI';
    }

    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic): Closure
    {
        $default_renderer = $dic->raw('ui.renderer');

        return function () use ($dic, $default_renderer) {
            return new \srag\Plugins\SrUI\UI\Renderer(
                $dic,
                $default_renderer($dic)
            );
        };
    }


    protected function init(): void
    {
        global $DIC;

        $this->ilias_version = new ILIASVersion(ILIAS_VERSION_NUMERIC);
        $this->storage_factory = new Factory();
        $this->configuration = new Configuration($this->getVersion());
        // $this->getLanguageHandler()->updateLanguages();
        try {
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::OUTSIDE_ILIAS,
                $this->isOutsideILIAS($DIC)
            );
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::ON_LOGIN_PAGE,
                $this->isOnLoginPage() || $this->isOnLoginRelatedPage($DIC)
            );

            $this->provider_collection->setModificationProvider(new ilSrUIModificationProvider($DIC, $this));
            $this->provider_collection->setMainBarProvider(new ilSrUIMainBarProvider($DIC, $this));
            if ($this->ilias_version->sevenOrNewer()) {
                $this->provider_collection->setMetaBarProvider(new ilSrUIMetaBarProvider($DIC, $this));
            }
        } catch (LogicException $l) {
        }
    }


    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }


    public function getILIASVersion(): ILIASVersion
    {
        return $this->ilias_version;
    }


    public function getStorageFactory(): Factory
    {
        return $this->storage_factory;
    }

    /**
     * @param Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(Container $DIC): bool
    {
        return (
            ($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0)
            && strtolower($DIC->http()->request()->getQueryParams()["baseClass"] ?? '') !== strtolower(
                ilRepositoryGUI::class
            )
        );
    }

    public function isOnLoginPage(): bool
    {
        return strpos($_SERVER['SCRIPT_NAME'], 'login.php') !== false;
    }

    public function isOnLoginRelatedPage(Container $DIC): bool
    {
        if ($DIC->http()->wrapper()->query()->has('baseClass')) {
            $to_str = $DIC->refinery()->kindlyTo()->string();
            $base_class = $DIC->http()->wrapper()->query()->retrieve('baseClass', $to_str);

            $fallback_cmd = "";
            if($DIC->http()->wrapper()->query()->has('fallbackCmd')) {
                $fallback_cmd = $DIC->http()->wrapper()->query()->retrieve('fallbackCmd', $to_str);
            }

            return (
                ($base_class === strtolower(ilStartUpGUI::class)) &&
                (
                    ($DIC->ctrl()->getCmd() === 'showLogout') || ($fallback_cmd === 'doStandardAuthentication') || ($DIC->ctrl()->getCmdClass() === strtolower(ilPasswordAssistanceGUI::class))
                )
            );
        }
        return false;
    }

    protected function afterInstall(): void
    {
        $this->onBoarding();
    }

    protected function afterUninstall(): void
    {
        $this->configuration->delete();
    }

    protected function afterUpdate(): void
    {
        $this->checkAndPerformConfigMigration();
    }


    protected function onBoarding(): void
    {
        $s = $this->storage_factory->storage();
        // LOGIN PAGE
        $this->configuration->get(CenteredPageConfig::NS)->loadData([
            CenteredPageConfig::F_SHOW_HEADER_LOGO => true,
            CenteredPageConfig::F_CENTERED_LOGIN_PAGE_CSS => file_get_contents(self::TEMPLATES_BASE . "login.css"),
            CenteredPageConfig::F_BACKGROUND_IMAGE => [
                $s->fromPath(
                    self::TEMPLATES_BASE . "background.jpg",
                    MimeType::IMAGE__SVG_XML
                )
            ],
        ]);
        $this->configuration->get(ShowComponentsConfig::NS)->loadData([
            ShowComponentsConfig::F_SHOW_HEADER => true,
            ShowComponentsConfig::F_SHOW_FOOTER => true,
            ShowComponentsConfig::F_SHOW_MAIN_BAR => false,
        ]);

        // HEADER
        $this->configuration->get(HeaderColorsConfiguration::NS)->loadData([
            HeaderColorsConfiguration::F_HEADER_BG_COLOR => 'white',
            HeaderColorsConfiguration::F_HEADER_BC_BG_COLOR => 'white',
            HeaderColorsConfiguration::F_HEADER_BC_TEXT_COLOR => '#202020',
            HeaderColorsConfiguration::F_TITLE_COLOR => '#202020',
        ]);
        $this->configuration->get(HeaderLogoConfiguration::NS)->loadData([
            HeaderLogoConfiguration::F_ICON => [
                $s->fromPath(
                    self::TEMPLATES_BASE . "HeaderIconPlus.svg",
                    MimeType::IMAGE__SVG_XML
                )
            ],
            HeaderLogoConfiguration::F_ICON_WIDTH => '45px',
        ]);

        $this->configuration->get(ResponsiveHeaderLogoConfiguration::NS)->loadData([
            ResponsiveHeaderLogoConfiguration::F_ICON => [
                $s->fromPath(
                    self::TEMPLATES_BASE . "HeaderIconPlus.svg",
                    MimeType::IMAGE__SVG_XML
                )
            ],
            HeaderLogoConfiguration::F_ICON_WIDTH => '',
        ]);
        $this->configuration->get(HeaderTitleConfiguration::NS)->loadData([
            HeaderTitleConfiguration::F_HEADER_TITLE => 'ILIAS+ 8'
        ]);

        // FOOTER
        $this->configuration->get(FooterColorsConfig::NS)->loadData([
            FooterColorsConfig::F_FOOTER_TEXT_COLOR => '#2D2D2D',
            FooterColorsConfig::F_FOOTER_BG_COLOR => 'white',
        ]);
        $this->configuration->get(AdditionalInfoConfig::NS)->loadData([
            AdditionalInfoConfig::F_SHOW_RENDERED_BY => true
        ]);

        // CARDS
        $this->configuration->get(SpecialContentFeaturesConfig::NS)->loadData([
            SpecialContentFeaturesConfig::F_FANCY_CARDS => true
        ]);


        $this->configuration->save();
    }


    public function checkAndPerformConfigMigration(): void
    {
        $migration_collection = new MigrationCollection(
            new Version($this->getVersion()),
            $this->configuration,
            new To1_6_0Migration(),
            new To1_7_0Migration()
        );

        $migration_collection->perform();
    }
}
