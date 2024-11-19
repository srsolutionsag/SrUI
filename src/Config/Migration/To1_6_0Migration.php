<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Migration;

use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Configuration;
use srag\Plugins\SrUI\Config\CloneInfo\ProdServerConfig;
use srag\Plugins\SrUI\Config\Content\SpecialContentFeaturesConfig;
use srag\Plugins\SrUI\Config\Footer\AdditionalInfoConfig;
use srag\Plugins\SrUI\Config\Footer\FooterColorsConfig;
use srag\Plugins\SrUI\Config\Header\HeaderColorsConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderLogoConfiguration;
use srag\Plugins\SrUI\Config\Header\HeaderTitleConfiguration;
use srag\Plugins\SrUI\Config\LoginPage\CenteredPageConfig;
use srag\Plugins\SrUI\Config\LoginPage\ShowComponentsConfig;

class To1_6_0Migration implements Migration
{
    public function appliesToVersionsPrior(): Version
    {
        return new Version('1.6.0');
    }

    public function perform(Configuration $configuration): void
    {
        $existing_file = rtrim(CLIENT_DATA_DIR, "/") . "/sr_ui_setting.php";
        if (file_exists($existing_file)) {
            $current_settings = include $existing_file;

            // PROD SERVER
            if (isset($current_settings['prod_system'])) {
                $configuration->get(ProdServerConfig::NS)->loadData([
                    ProdServerConfig::F_NON_PROD_MESSAGE => $current_settings['non_prod_message'] ?? '',
                    ProdServerConfig::F_PROD_HOSTNAMES => $current_settings['prod_hostname'] ?? '',
                ]);
            }
            // CONTENT
            $configuration->get(SpecialContentFeaturesConfig::NS)->loadData([
                SpecialContentFeaturesConfig::F_FANCY_CARDS => $current_settings['fancy_cards'],
                SpecialContentFeaturesConfig::F_FANCY_TABS => $current_settings['fancy_tabs'],
            ]);

            // FOOTER
            $configuration->get(AdditionalInfoConfig::NS)->loadData([
                AdditionalInfoConfig::F_SHOW_RENDERED_BY => $current_settings['show_rendered_by']
            ]);
            $configuration->get(FooterColorsConfig::NS)->loadData([
                FooterColorsConfig::F_FOOTER_BG_COLOR => $current_settings['footer_bg_color'],
                FooterColorsConfig::F_FOOTER_TEXT_COLOR => $current_settings['footer_text_color'],
            ]);

            // HEADER
            $configuration->get(HeaderColorsConfiguration::NS)->loadData([
                HeaderColorsConfiguration::F_HEADER_BC_TEXT_COLOR => $current_settings['header_bc_text_color'],
                HeaderColorsConfiguration::F_HEADER_BC_BG_COLOR => $current_settings['header_bc_bg_color'],
                HeaderColorsConfiguration::F_HEADER_BG_COLOR => $current_settings['header_bg_color'],
            ]);
            $configuration->get(HeaderLogoConfiguration::NS)->loadData([
                HeaderLogoConfiguration::F_ICON => $current_settings['icon'] ?? null,
                HeaderLogoConfiguration::F_ICON_WIDTH => $current_settings['icon_width'] ?? null,
            ]);
            $configuration->get(HeaderTitleConfiguration::NS)->loadData([
                HeaderTitleConfiguration::F_HEADER_TITLE => $current_settings['header_title'],
            ]);

            // LOGIN PAGE
            $configuration->get(CenteredPageConfig::NS)->loadData([
                CenteredPageConfig::F_SHOW_HEADER_LOGO => $current_settings['show_header_logo'] ?? true,
                CenteredPageConfig::F_BACKGROUND_IMAGE => $current_settings['background_image'] ?? '',
                CenteredPageConfig::F_CENTERED_LOGIN_PAGE_CSS => $current_settings['centered_login_page_css'] ?? '',
            ]);
            $configuration->get(ShowComponentsConfig::NS)->loadData([
                ShowComponentsConfig::F_SHOW_FOOTER => $current_settings['show_footer'] ?? true,
                ShowComponentsConfig::F_SHOW_MAIN_BAR => $current_settings['show_main_bar'] ?? true,
                ShowComponentsConfig::F_SHOW_HEADER => $current_settings['show_header'] ?? true,
            ]);
        }
        $configuration->save();
    }
}
