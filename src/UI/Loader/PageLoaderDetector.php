<?php

namespace srag\Plugins\SrUI\UI\Loader;

use Closure;
use ILIAS\UI\Component\Card\Card;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Component\Card\ZoomableCardRenderer;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Login as LoginPage;
use ILIAS\UI\Implementation\Component\Layout\Page\Login\Renderer as LoginPageRender;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\UI\Implementation\Render\ComponentRenderer;
use ILIAS\UI\Renderer;
use Pimple\Container;

/**
 * Class PageLoaderDetector
 * @package srag\Plugins\SrUI\UI\Loader
 */
class PageLoaderDetector extends \srag\Plugins\SrUI\UI\Loader\AbstractLoaderDetector
{
    
    /**
     * @return Closure
     */
    public static function exchange() : Closure
    {
        global $DIC;
        $previous_renderer = Closure::bind(function () {
            return $this->raw("ui.renderer");
        }, $DIC, Container::class)();
        
        return function () use ($previous_renderer, $DIC) : Renderer {
            $previous_renderer = $previous_renderer($DIC);
            
            if ($previous_renderer instanceof DefaultRenderer) {
                $previous_renderer_loader = Closure::bind(function () {
                    return $this->component_renderer_loader;
                }, $previous_renderer, DefaultRenderer::class)();
            } else {
                $previous_renderer_loader = null;
            }
            
            return new DefaultRenderer(new self($previous_renderer_loader));
        };
    }
    
    /**
     * @inheritDoc
     */
    public function getRendererFor(Component $component, array $contexts) : ComponentRenderer
    {
        global $DIC;
        switch (true) {
            case $component instanceof LoginPage:
                return new LoginPageRender(
                    $DIC["ui.factory"],
                    $DIC["ui.template_factory"],
                    $DIC["lng"],
                    $DIC["ui.javascript_binding"],
                    $DIC['refinery']
                );
            case $component instanceof Card:
                return new ZoomableCardRenderer(
                    $DIC["ui.factory"],
                    $DIC["ui.template_factory"],
                    $DIC["lng"],
                    $DIC["ui.javascript_binding"],
                    $DIC['refinery']
                );
            default:
                return parent::getRendererFor($component, $contexts);
        }
    }
}
