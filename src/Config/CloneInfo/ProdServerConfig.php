<?php

namespace srag\Plugins\SrUI\Config\CloneInfo;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ProdServerConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ProdServerConfig extends BaseNestedConfig
{
    const F_PROD_SYSTEM = 'prod_system';
    const F_PROD_HOSTNAMES = 'prod_hostname';
    const F_NON_PROD_MESSAGE = 'non_prod_message';
    const F_SEND_NON_INDEX_HEADERS = 'send_non_index_headers';

    public function __construct()
    {
        parent::__construct(self::F_PROD_SYSTEM);
    }

}
