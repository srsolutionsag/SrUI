<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrUI\UI;

use ILIAS\UI\Implementation\Render\DecoratedRenderer;
use srag\Plugins\SrUI\UI\Login\Login;
use ILIAS\DI\Container;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Renderer extends DecoratedRenderer
{
    public function __construct(private Container $dic, \ILIAS\UI\Renderer $default)
    {
        parent::__construct($default);
    }

    protected function manipulateRendering($component, \ILIAS\UI\Renderer $root): ?string
    {
        if ($component instanceof Login) {
            $renderer = new \srag\Plugins\SrUI\UI\Login\Renderer(
                $this->dic["ui.factory"],
                $this->dic["ui.template_factory"],
                $this->dic["lng"],
                $this->dic["ui.javascript_binding"],
                $this->dic['refinery'],
                $this->dic["ui.pathresolver"],
                $this->dic["ui.data_factory"],
                $this->dic["help.text_retriever"],
                $this->dic["ui.upload_limit_resolver"],
            );
            return $renderer->render($component, $this);
        }

        return null;
    }
}
