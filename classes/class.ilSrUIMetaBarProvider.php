<?php

/** @noRector  */
require_once(__DIR__ . '/../vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\MetaBar\Provider\AbstractStaticMetaBarPluginProvider;
use srag\Plugins\SrUI\Artifact\Artifact;

/**
 * Class ilSrUIMetaBarProvider
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIMetaBarProvider extends AbstractStaticMetaBarPluginProvider
{
    /**
     * @return Artifact
     */
    private function getArtifact(): Artifact
    {
        static $settings;
        if (!$settings) {
            $settings = Artifact::getInstance('sr_ui_setting');
        }
        return $settings;
    }

    public function getMetaBarItems(): array
    {
        $ref_id = $_GET['ref_id'] ?? null;
        $obj_id = $ref_id !== null ? \ilObject2::_lookupObjId($ref_id) : '-';

        $factory = $this->dic->ui()->factory();

        $items = $factory->listing()->characteristicValue()->text([
            'Reference-ID' => $ref_id ?? '-',
            'Object-ID' => (string) $obj_id,
            'Call-History' => implode(
                '<br>',
                array_map(function (array $i): string {
                    return $i['cmdClass'] ?? '';
                }, $this->dic->ctrl()->getCallHistory())
            )
        ]);

        $panel = $factory->panel()->secondary()->legacy(
            'Debug Infos',
            $factory->legacy($this->dic->ui()->renderer()->render($items))
        );

        $content = $this->dic->ui()->renderer()->render($panel);

        return [
            $this->meta_bar->topLegacyItem($this->if->identifier('debug'))
                           ->withLegacyContent($factory->legacy($content))
                           ->withSymbol($factory->symbol()->glyph()->bulletlist())
                           ->withVisibilityCallable(function (): bool {
                               return $this->dic->rbac()->review()->isAssigned($this->dic->user()->getId(), 2);
                           })
                           ->withTitle('DEBUG')
        ];
    }
}
