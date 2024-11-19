<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\UI\Login;

use ILIAS\UI\Component;
use ILIAS\UI\Implementation\Render\ilTemplateWrapper;
use ILIAS\Data\URI;

/**
 * Class Renderer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Renderer extends \ILIAS\UI\Implementation\Component\Layout\Page\Renderer
{
    public const TEMPLATE_BASE = \ilSrUIPlugin::TEMPLATES_BASE;

    /**
     * @param mixed $default_renderer
     */
    public function render(Component\Component $component, $default_renderer): string
    {
        if ($component instanceof Login) {
            return $this->renderLoginPage($component, $default_renderer);
        }
        if ($component instanceof AdvancedFooter) {
            return $this->renderAdvancedFooter($component, $default_renderer);
        }
        return $default_renderer->render($component);
    }

    private function renderAdvancedFooter(AdvancedFooter $component, mixed $default_renderer): string
    {
        global $DIC;
        $tpl = new ilTemplateWrapper(
            $DIC->ui()->mainTemplate(),
            new \ilTemplate(self::TEMPLATE_BASE . 'tpl.advanced_footer.html', true, true)
        );

        $links = $component->getLinks();
        $modalsWithTriggers = $component->getModals();
        $links = array_merge($links, array_column($modalsWithTriggers, 1));

        if ($links !== []) {
            $link_list = $this->getUIFactory()->listing()->unordered($links);
            $tpl->setVariable('LINKS', $default_renderer->render($link_list));
        }

        if ($modalsWithTriggers !== []) {
            $tpl->setVariable(
                'MODALS',
                $default_renderer->render(
                    array_column($modalsWithTriggers, 0)
                )
            );
        }

        $tpl->setVariable('TEXT', $component->getText());

        $perm_url = $component->getPermanentURL();
        if ($perm_url instanceof URI) {
            $tpl->setVariable('PERMANENT_LABEL', $this->txt('perma_link'));
            $tpl->setVariable('PERMANENT_ACTION', (string) $perm_url);
        }
        return $tpl->get();
    }

    protected function renderLoginPage(Login $component, mixed $default_renderer): string
    {
        global $DIC;
        $tpl = new ilTemplateWrapper(
            $DIC->ui()->mainTemplate(),
            new \ilTemplate(self::TEMPLATE_BASE . 'tpl.login_page.html', true, true)
        );
        $tpl->setVariable('FAVICON_PATH', $component->getFaviconPath());

        $id = $this->bindJavaScript($component);
        $tpl->setVariable("ID", $id);

        if ($component->hasOverlay()) {
            $tpl->setVariable('OVERLAY', $default_renderer->render($component->getOverlay()));
        }
        if ($component->hasMetabar()) {
            $tpl->setVariable('METABAR', $default_renderer->render($component->getMetabar()));
        }
        if ($component->hasMainbar()) {
            $tpl->setVariable('MAINBAR', $default_renderer->render($component->getMainbar()));
            // There is a roadmap entry for this.
            $slates_cookie = $_COOKIE[self::COOKIE_NAME_SLATES_ENGAGED] ?? '';
            if ($slates_cookie && json_decode((string) $slates_cookie, true)['engaged']) {
                $tpl->touchBlock('slates_engaged');
            }
        }
        if ($component->hasModeInfo()) {
            $tpl->setVariable('MODEINFO', $default_renderer->render($component->getModeInfo()));
        }
        if ($component->hasSystemInfos()) {
            $tpl->setVariable('SYSTEMINFOS', $default_renderer->render($component->getSystemInfos()));
        }

        $breadcrumbs = $component->getBreadcrumbs();
        if ($breadcrumbs && $breadcrumbs->getItems()) {
            $tpl->setVariable('BREADCRUMBS', $default_renderer->render($breadcrumbs));

            $dropdown = $this->convertBreadcrumbsToDropdownLocator($breadcrumbs);
            $tpl->setVariable('HEADER_BREADCRUMBS', $default_renderer->render($dropdown));
        }

        if ($component->hasLogo()) {
            $tpl->setVariable('LOGO', $default_renderer->render($component->getLogo()));
        }
        if ($component->hasResponsiveLogo()) {
            $tpl->setVariable('RESPONSIVE_LOGO', $default_renderer->render($component->getResponsiveLogo()));
        } elseif ($component->hasLogo()) {
            $tpl->setVariable('RESPONSIVE_LOGO', $default_renderer->render($component->getLogo()));
        }

        $tpl->setVariable("TITLE", $component->getTitle());
        $tpl->setVariable("SHORT_TITLE", $component->getShortTitle());
        $tpl->setVariable("VIEW_TITLE", $component->getViewTitle());
        $tpl->setVariable("LANGUAGE", $this->getLangKey());
        $tpl->setVariable("TEXT_DIRECTION", $component->getTextDirection());
        $tpl->setVariable('CONTENT', $default_renderer->render($component->getContent()));

        if ($component->getWithHeaders()) {
            $tpl = $this->setHeaderVars($tpl, $component->getIsUIDemo());
        }

        $tpl->setVariable(
            'META_DATA',
            $this->getDataFactory()->htmlMetadata()->collection(
                $component->getMetaData()
            )->toHtml()
        );

        // Specific for Login Page
        if ($component->getSettings()->isShowFooter() && $component->hasFooter()) {
            $tpl->setVariable('FOOTER', $default_renderer->render($component->getFooter()));
        }

        if ($component->getSettings()->getBackgroundImage() !== '' && $component->getSettings()->getBackgroundImage(
        ) !== '0') {
            $tpl->setVariable('BACKGROUND_IMAGE', $component->getSettings()->getBackgroundImage());
        }

        if ($component->getSettings()->isShowHeaderLogo()) {
            $tpl->setVariable("LOGO_LOGIN", $default_renderer->render($component->getLogo()));
        }

        return $tpl->get();
    }
}
