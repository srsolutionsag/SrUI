<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\SrUI\Config\Migration;

use srag\Plugins\SrUI\Version;
use srag\Plugins\SrUI\Config\Configuration;

/**
 * Class MigrationCollection
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MigrationCollection
{
    /**
     * @var Migration[]
     */
    private array $migrations = [];
    private Version $current_version;
    protected Configuration $configuration;

    public function __construct(Version $current_version, Configuration $configuration, ...$migrations)
    {
        $this->current_version = $current_version;
        $this->configuration = $configuration;
        $this->migrations = $migrations;
    }

    public function perform(): void
    {
        // sort by version
        usort($this->migrations, fn(Migration $m, Migration $m_2): int => $m->appliesToVersionsPrior()->isNewerThan($m_2->appliesToVersionsPrior()) ? 1 : -1);
        $current_config_version = $this->configuration->getStoredConfigVersion();
        foreach ($this->migrations as $migration) {
            if ($current_config_version->isOlderThan($migration->appliesToVersionsPrior())) {
                $migration->perform($this->configuration);
            }
        }
    }
}
