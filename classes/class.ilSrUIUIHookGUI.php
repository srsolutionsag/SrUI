<?php

use srag\Plugins\SrUI\Config\Content\Config;

/**
 * Class ilSrUIUIHookGUI
 * @author studer + raimann ag - Team Core 1 <support-core1@studer-raimann.ch>
 */
class ilSrUIUIHookGUI extends ilUIHookPluginGUI
{
    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;
    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;
    protected static $subtabs_html = '';

    public function getHTML(
        string $a_comp,
        string $a_part,
        array $a_par = []
    ): array {
        $content = $this->plugin_object->getConfiguration()->contentConfig();
        if (!$content->hasFancyTabs()) {
            return parent::getHTML($a_comp, $a_part, $a_par);
        }
        if ($a_part === "template_get"
            && $a_par['tpl_id'] === "Services/UIComponent/Tabs/tpl.sub_tabs.html"
        ) {
            self::$subtabs_html = $a_par['html'];
            return ['mode' => self::REPLACE, 'html' => ''];
        }

        if ($a_part === "template_get"
            && $a_par['tpl_id'] === "Services/UIComponent/Tabs/tpl.tabs.html"
        ) {
            global $DIC;

            $tabs = $DIC->tabs()->target;
            $subtabs = $DIC->tabs()->sub_target;

            $tpl = new ilTemplate(ilSrUIPlugin::TEMPLATES_BASE . 'tpl.tabs.html', true, true);
            foreach ($tabs as $tab) {
                $tpl->setCurrentBlock('tab');
                $tpl->setVariable('TITLE', $tab['dir_text'] ? $tab['text'] : $DIC->language()->txt($tab['text']));
                $tpl->setVariable('ID', $tab['id']);
                $tpl->setVariable('HREF', $tab['link']);
                if ($tab['activate']) {
                    $tpl->touchBlock('active');
                }
                $tpl->parseCurrentBlock();
            }

            return ['mode' => self::REPLACE, 'html' => $tpl->get()];

            $ui_factory = $DIC->ui()->factory();
            $ui_renderer = $DIC->ui()->renderer();
            $slate = $ui_factory->mainControls()->slate()->legacy(
                'Tabs',
                $ui_factory->symbol()->glyph()->comment(),
                $ui_factory->legacy($a_par['html'] . self::$subtabs_html)
            );
            $triggerer = $ui_factory->button()->bulky(
                $slate->getSymbol(),
                $slate->getName(),
                '#'
            )->withOnClick($slate->getToggleSignal());

            return ['mode' => self::REPLACE, 'html' => $ui_renderer->render([$triggerer, $slate])];
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
    }
}
