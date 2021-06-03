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
    /**
     * @param Component\Card\Card $component
     * @param RendererInterface   $default_renderer
     * @return string
     */
    public function render(Component\Component $component, RendererInterface $default_renderer)
    {
        return parent::render($component, $default_renderer);
    }
    
}
