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
    const TEMPLATE_BASE = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/';
    
    public function render(Component\Component $component, RendererInterface $default_renderer)
    {
        return $this->renderLoginPage($component, $default_renderer);
    }

    protected function renderLoginPage(Login $component, RendererInterface $default_renderer)
    {
        $tpl = new \ilTemplate(self::TEMPLATE_BASE . 'tpl.login_page.html', true, true);

        if ($component->hasMetabar()) {
            $tpl->setVariable('METABAR', $default_renderer->render($component->getMetabar()));
        }
        if ($component->getSettings()->isShowMainBar() && $component->hasMainbar()) {
            $tpl->setVariable('MAINBAR', $default_renderer->render($component->getMainbar()));
        }
        if ($component->hasModeInfo()) {
            $tpl->setVariable('MODEINFO', $default_renderer->render($component->getModeInfo()));
        }

        $breadcrumbs = $component->getBreadcrumbs();
        if ($breadcrumbs && $breadcrumbs->getItems()) {
            $tpl->setVariable('BREADCRUMBS', $default_renderer->render($breadcrumbs));

            $dropdown = $this->convertBreadcrumbsToDropdownLocator($breadcrumbs);
            $tpl->setVariable('HEADER_BREADCRUMBS', $default_renderer->render($dropdown));
        }
        if ($component->hasLogo()) {
            $logo = $component->getLogo();
            if ($logo) {
                $tpl->setVariable("LOGO", $default_renderer->render($logo));
            }
        }
        

        $slates_cookie = $_COOKIE[self::COOKIE_NAME_SLATES_ENGAGED];
        if ($slates_cookie && json_decode($slates_cookie, true)['engaged']) {
            $tpl->touchBlock('slates_engaged');
        }

        $tpl->setVariable("TITLE", $component->getTitle());
        $tpl->setVariable("SHORT_TITLE", $component->getShortTitle());
        $tpl->setVariable("VIEW_TITLE", $component->getViewTitle());
        $tpl->setVariable("LANGUAGE", $this->getLangKey());
        $tpl->setVariable('CONTENT', $default_renderer->render($component->getContent()));
    
        if ($component->getSettings()->getBackgroundImage()) {
            $tpl->setVariable('BACKGROUND_IMAGE', $component->getSettings()->getBackgroundImage());
        }
    
    
        if ($component->getSettings()->isShowFooter() && $component->hasFooter()) {
            $tpl->setVariable('FOOTER', $default_renderer->render($component->getFooter()));
        }

        if ($component->getWithHeaders()) {
            $tpl = $this->setHeaderVars($tpl);
        }

        return $tpl->get();
    }

}
