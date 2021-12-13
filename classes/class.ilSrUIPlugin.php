<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\DI\Container;
use srag\Plugins\SrUI\UI\Loader\PageLoaderDetector;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;
use srag\Plugins\SrUI\Config\Content\Config;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;
use srag\Plugins\SrUI\ResourceStorage\Factory;
use srag\Plugins\SrUI\Config\Content\SpecialContentFeaturesConfig;

/**
 * Class ilSrUIPlugin
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIPlugin extends ilUserInterfaceHookPlugin
{
    public const PLUGIN_BASE = __DIR__ . '/../';
    public const TEMPLATES_BASE = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/";
    public const OUTSIDE_ILIAS = 'anon';
    public const ON_LOGIN_PAGE = 'on_login_page';
    /**
     * @var Configuration
     */
    protected $configuration;

    public function getPluginName()
    {
        return 'SrUI';
    }

    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return PageLoaderDetector::exchange();
    }

    public function __construct()
    {
        global $DIC;

        parent::__construct();
        $this->configuration = new Configuration($this->getVersion());

        try {
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::OUTSIDE_ILIAS,
                $this->isOutsideILIAS($DIC)
            );
            $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(
                self::ON_LOGIN_PAGE,
                $this->isOnLoginPage()
            );

            $this->provider_collection->setModificationProvider(new ilSrUIModificationProvider($DIC, $this));
            $this->provider_collection->setMainBarProvider(new ilSrUIMainBarProvider($DIC, $this));
        } catch (LogicException $l) {

        }

    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(Container $DIC) : bool
    {
        return (
            ($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0)
            && strtolower($DIC->http()->request()->getQueryParams()["baseClass"]) !== strtolower(ilRepositoryGUI::class)

        );
    }

    public function isOnLoginPage() : bool
    {
        return strpos($_SERVER['SCRIPT_NAME'], 'login.php') !== false;
    }

    protected function afterInstall()
    {
        $this->onBoarding();
    }

    protected function afterUpdate()
    {
        $this->checkAndPerformConfigMigration();
    }

    protected function onBoarding() : void
    {
        $s = Factory::storage();
        // LOGIN PAGE
        $this->configuration->get(CenteredPageConfig::NS)->loadData([
            CenteredPageConfig::F_SHOW_HEADER_LOGO => true,
            CenteredPageConfig::F_CENTERED_LOGIN_PAGE_CSS => file_get_contents(self::TEMPLATES_BASE . "login.css"),
            CenteredPageConfig::F_BACKGROUND_IMAGE => [
                $s->fromPath(
                    self::TEMPLATES_BASE . "rose-petals.svg",
                    ilMimeTypeUtil::IMAGE__SVG_XML
                )
            ],
        ]);
        $this->configuration->get(ShowComponentsConfig::NS)->loadData([
            ShowComponentsConfig::F_SHOW_HEADER => false,
            ShowComponentsConfig::F_SHOW_FOOTER => false,
            ShowComponentsConfig::F_SHOW_MAIN_BAR => false,
        ]);

        // HEADER
        $this->configuration->get(HeaderColorsConfiguration::NS)->loadData([
            HeaderColorsConfiguration::F_HEADER_BG_COLOR => '#202020',
            HeaderColorsConfiguration::F_HEADER_BC_BG_COLOR => '#202020',
            HeaderColorsConfiguration::F_HEADER_BC_TEXT_COLOR => '#fdfdfd',
            HeaderColorsConfiguration::F_TITLE_COLOR => '#fdfdfd',
        ]);
        $this->configuration->get(HeaderLogoConfiguration::NS)->loadData([
            HeaderLogoConfiguration::F_ICON => [
                $s->fromPath(
                    self::TEMPLATES_BASE . "HeaderIconPlus.svg",
                    ilMimeTypeUtil::IMAGE__SVG_XML)
            ],
            HeaderLogoConfiguration::F_ICON_WIDTH => '45px'
        ]);
        $this->configuration->get(HeaderTitleConfiguration::NS)->loadData([
            HeaderTitleConfiguration::F_HEADER_TITLE => 'ILIAS+ 6'
        ]);

        // FOOTER
        $this->configuration->get(FooterColorsConfig::NS)->loadData([
            FooterColorsConfig::F_FOOTER_TEXT_COLOR => '#F6F6F6',
            FooterColorsConfig::F_FOOTER_BG_COLOR => '#2D2D2D',
        ]);
        $this->configuration->get(AdditionalInfoConfig::NS)->loadData([
            AdditionalInfoConfig::F_SHOW_RENDERED_BY => true
        ]);

        $this->configuration->save();
    }

    protected function checkAndPerformConfigMigration() : void
    {
        if ($this->configuration->getStorage()->needsUpdate()) {
            switch ($this->configuration->getStorage()->getStoredVersion()) {
                case '':
                    $existing_file = rtrim(CLIENT_DATA_DIR, "/") . "/sr_ui_setting.php";
                    if (file_exists($existing_file)) {
                        $current_settings = include $existing_file;

                        // PROD SERVER
                        if (isset($current_settings['prod_system'])) {
                            $this->configuration->get(ProdServerConfig::NS)->loadData([
                                ProdServerConfig::F_NON_PROD_MESSAGE => $current_settings['non_prod_message'] ?? '',
                                ProdServerConfig::F_PROD_HOSTNAMES => $current_settings['prod_hostname'] ?? '',
                            ]);
                        }
                        // CONTENT
                        $this->configuration->get(SpecialContentFeaturesConfig::NS)->loadData([
                            SpecialContentFeaturesConfig::F_FANCY_CARDS => $current_settings['fancy_cards'],
                            SpecialContentFeaturesConfig::F_FANCY_TABS => $current_settings['fancy_tabs'],
                        ]);

                        // FOOTER
                        $this->configuration->get(AdditionalInfoConfig::NS)->loadData([
                            AdditionalInfoConfig::F_SHOW_RENDERED_BY => $current_settings['show_rendered_by']
                        ]);
                        $this->configuration->get(FooterColorsConfig::NS)->loadData([
                            FooterColorsConfig::F_FOOTER_BG_COLOR => $current_settings['footer_bg_color'],
                            FooterColorsConfig::F_FOOTER_TEXT_COLOR => $current_settings['footer_text_color'],
                        ]);

                        // HEADER
                        $this->configuration->get(HeaderColorsConfiguration::NS)->loadData([
                            HeaderColorsConfiguration::F_HEADER_BC_TEXT_COLOR => $current_settings['header_bc_text_color'],
                            HeaderColorsConfiguration::F_HEADER_BC_BG_COLOR => $current_settings['header_bc_bg_color'],
                            HeaderColorsConfiguration::F_HEADER_BG_COLOR => $current_settings['header_bg_color'],
                        ]);
                        $this->configuration->get(HeaderLogoConfiguration::NS)->loadData([
                            HeaderLogoConfiguration::F_ICON => $current_settings['icon'],
                            HeaderLogoConfiguration::F_ICON_WIDTH => $current_settings['icon_width'],
                        ]);
                        $this->configuration->get(HeaderTitleConfiguration::NS)->loadData([
                            HeaderTitleConfiguration::F_HEADER_TITLE => $current_settings['header_title'],
                        ]);

                        // LOGIN PAGE
                        $this->configuration->get(CenteredPageConfig::NS)->loadData([
                            CenteredPageConfig::F_SHOW_HEADER_LOGO => $current_settings['show_header_logo'],
                            CenteredPageConfig::F_BACKGROUND_IMAGE => $current_settings['background_image'],
                            CenteredPageConfig::F_CENTERED_LOGIN_PAGE_CSS => $current_settings['centered_login_page_css'],
                        ]);
                        $this->configuration->get(ShowComponentsConfig::NS)->loadData([
                            ShowComponentsConfig::F_SHOW_FOOTER => $current_settings['show_footer'],
                            ShowComponentsConfig::F_SHOW_MAIN_BAR => $current_settings['show_main_bar'],
                            ShowComponentsConfig::F_SHOW_HEADER => $current_settings['show_header'],
                        ]);
                    }
                    break;
            }
            $this->configuration->save();
        }
    }

}
