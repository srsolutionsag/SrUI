<?php

namespace ILIAS\UI\Implementation\Component\Layout\Page\Login;

use ILIAS\UI\Component;
use ILIAS\UI\Renderer as RendererInterface;

/**
 * Class Renderer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Renderer extends \ILIAS\UI\Implementation\Component\Layout\Page\Renderer
{
    public function render(Component\Component $component, RendererInterface $default_renderer)
    {

        return $this->renderLoginPage($component, $default_renderer);
    }

    protected function renderLoginPage(Component\Layout\Page\Standard $component, RendererInterface $default_renderer)
    {
        $tpl = new \ilTemplate('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/tpl.login_page.html', true, true);

        if ($component->hasLogo()) {
            $logo = $component->getLogo();
            if ($logo) {
                $tpl->setVariable("LOGO", $default_renderer->render($logo));
            }
        }

        if ($component->hasMetabar()) {
            $tpl->setVariable('METABAR', $default_renderer->render($component->getMetabar()));
        }

        $tpl->setVariable("TITLE", $component->getTitle());
        $tpl->setVariable("SHORT_TITLE", $component->getShortTitle());
        $tpl->setVariable("VIEW_TITLE", $component->getViewTitle());
        $tpl->setVariable("LANGUAGE", $this->getLangKey());
        $tpl->setVariable('CONTENT', $default_renderer->render($component->getContent()));

        if ($component->getWithHeaders()) {
            $tpl = $this->setHeaderVars($tpl, $component->getIsUIDemo());
        }

        return $tpl->get();
    }

}
