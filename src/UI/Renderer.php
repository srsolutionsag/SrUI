<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrUI\UI;

use ILIAS\UI\Implementation\Render\DecoratedRenderer;
use srag\Plugins\SrUI\UI\Login\Login;
use ILIAS\DI\Container;
use ILIAS\UI\Implementation\Render\ComponentRenderer;
use ILIAS\UI\Component\Card\Card;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Renderer extends DecoratedRenderer
{
    /**
     * @var Container
     */
    private $dic;

    public function __construct(Container $dic, \ILIAS\UI\Renderer $default)
    {
        $this->dic = $dic;
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
                $this->dic["ui.pathresolver"]
            );
            return $renderer->render($component, $this);
        }

        return null;
    }
}
