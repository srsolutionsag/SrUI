<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use srag\Plugins\SrUI\UI\Renderer;
use ILIAS\DI\Container;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\ILIASVersion;
use srag\Plugins\SrUI\Config\Migration\MigrationCollection;
use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Migration\To1_6_0Migration;
use srag\Plugins\SrUI\Config\Migration\To1_7_0Migration;

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

    protected Configuration $configuration;
    protected ?ILIASVersion $ilias_version = null;
    protected Factory $storage_factory;

    public function getPluginName(): string
    {
        return 'SrUI';
    }

    public function exchangeUIRendererAfterInitialization(Container $dic): Closure
    {
        $default_renderer = $dic->raw('ui.renderer');

        return static fn(): Renderer => new Renderer(
            $dic,
            $default_renderer($dic)
        );
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
        } catch (LogicException) {
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
        return str_contains((string) $_SERVER['SCRIPT_NAME'], 'login.php');
    }

    public function isOnLoginRelatedPage(Container $DIC): bool
    {
        if ($DIC->http()->wrapper()->query()->has('baseClass')) {
            $to_str = $DIC->refinery()->kindlyTo()->string();
            $base_class = $DIC->http()->wrapper()->query()->retrieve('baseClass', $to_str);

            $fallback_cmd = "";
            if ($DIC->http()->wrapper()->query()->has('fallbackCmd')) {
                $fallback_cmd = $DIC->http()->wrapper()->query()->retrieve('fallbackCmd', $to_str);
            }

            return (
                ($base_class === strtolower(ilStartUpGUI::class)) &&
                (
                    ($DIC->ctrl()->getCmd(
                    ) === 'showLogout') || ($fallback_cmd === 'doStandardAuthentication') || ($DIC->ctrl(
                    )->getCmdClass() === strtolower(ilPasswordAssistanceGUI::class))
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
        // import default configuration
        $zip = new ZipArchive();
        $zip->open(__DIR__ . "/../templates/sr_ui_config.zip");
        $this->configuration->import($zip);
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
