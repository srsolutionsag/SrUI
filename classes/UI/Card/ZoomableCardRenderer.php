<?php

namespace ILIAS\UI\Implementation\Component\Card;

use ILIAS\UI\Component;
use ILIAS\UI\Renderer as RendererInterface;

/**
 * Class ZoomableCardRenderer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ZoomableCardRenderer extends Renderer
{
    protected function getTemplatePath($name)
    {
        return "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/tpl.zoomable_card.html";
    }
    
}
