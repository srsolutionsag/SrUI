<?php

require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/vendor/autoload.php');

use ILIAS\GlobalScreen\Scope\Layout\Factory\PageBuilderModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;
use ILIAS\UI\Component\Layout\Page\Page;
use srag\Plugins\SrUI\UI\Loader\PageLoaderDetector;

/**
 * Class ilSrUIPlugin
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrUIPlugin extends ilUserInterfaceHookPlugin
{
    public const  LOGIN_TEMPLATE_WRAPPER = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/tpl.login_page.html';
    public const LOGIN_CSS = "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SrUI/templates/login.css";
    const OUTSIDE_ILIAS = 'anon';

    public function getPluginName()
    {
        return 'SrUI';
    }

    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic) : Closure
    {
        return PageLoaderDetector::exchange();
    }

    public function __construct()
    {
        global $DIC;

        parent::__construct();
        $DIC->globalScreen()->layout()->meta()->addCss(self::LOGIN_CSS);
        $DIC->globalScreen()->tool()->context()->current()->addAdditionalData(self::OUTSIDE_ILIAS, $this->isOutsideILIAS($DIC));

        $this->provider_collection->setModificationProvider(new class($DIC, $this) extends AbstractModificationPluginProvider {
            public function isInterestedInContexts() : ContextCollection
            {
                return $this->context_collection->main()->external();
            }

            public function getPageBuilderDecorator(CalledContexts $screen_context_stack) : ?PageBuilderModification
            {
                if ($screen_context_stack->current()->getAdditionalData()->is(ilSrUIPlugin::OUTSIDE_ILIAS, true)) {
                    /** @noinspection PhpIncompatibleReturnTypeInspection */
                    return $this->factory->page()
                                         ->withLowPriority()
                                         ->withModification(
                                             static function (PagePartProvider $parts) : Page {
                                                 global $DIC;

                                                 $page_content = new ilTemplate(ilSrUIPlugin::LOGIN_TEMPLATE_WRAPPER, true, true);
                                                 $page_content->setVariable('CONTENT', $DIC->ui()->renderer()->render($parts->getContent()));
                                                 $f = $DIC->ui()->factory();

                                                 return $f->layout()->page()->standard(
                                                     [$f->legacy($page_content->get())]
                                                     , $parts->getMetaBar()
                                                     , null
                                                     , null
                                                     , $parts->getLogo()
                                                     , null
                                                     , $parts->getTitle()
                                                     , $parts->getShortTitle()
                                                     , $parts->getViewTitle()
                                                 );
                                             }
                                         );
                }

                return null;
            }

        });
    }

    /**
     * @param \ILIAS\DI\Container $DIC
     * @return bool
     */
    public function isOutsideILIAS(\ILIAS\DI\Container $DIC) : bool
    {
        return (($DIC->user()->isAnonymous() || $DIC->user()->getId() === 0) && strtolower($DIC->http()->request()->getQueryParams()["baseClass"]) !== strtolower(ilRepositoryGUI::class));
    }

}
