<?php

namespace ILIAS\UI\Implementation\Component\Card;

use ILIAS\UI\Component;
use ILIAS\UI\Renderer as RendererInterface;
use srag\Plugins\SrUI\Version;

/**
 * Class ZoomableCardRenderer
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ZoomableCardRenderer extends Renderer
{
    const TEMPLATES = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/";
    /**
     * @var bool
     */
    private $has_fix = null;
    
    protected function getTemplatePath($name)
    {
        if ($this->hasFixedCardsInCore()) {
            return parent::getTemplatePath($name);
        }
        return self::TEMPLATES . "tpl.zoomable_card.html";
    }
    
    private function hasFixedCardsInCore() : bool
    {
        if ($this->has_fix === null) {
            $version = new Version(ILIAS_VERSION_NUMERIC);
            $this->has_fix = $version->hasCardFix();
        }
        return $this->has_fix;
    }
}
