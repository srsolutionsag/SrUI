<?php

namespace srag\Plugins\SrUI\Config\CloneInfo;

use srag\Plugins\SrUI\Config\BaseNestedConfig;

/**
 * Class ProdServerConfig
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ProdServerConfig extends BaseNestedConfig
{
    const NS = 'prod_system';
    const F_PROD_HOSTNAMES = 'prod_hostname';
    const F_NON_PROD_MESSAGE = 'non_prod_message';
    const F_NON_PROD_MESSAGE_TITLE = 'non_prod_message_title';
    const F_SEND_NON_INDEX_HEADERS = 'send_non_index_headers';

    public function __construct()
    {
        parent::__construct(self::NS);
    }

    public function getData()
    {
        return [
            self::F_PROD_HOSTNAMES => $this->data[self::F_PROD_HOSTNAMES] ?? '',
            self::F_NON_PROD_MESSAGE => $this->data[self::F_NON_PROD_MESSAGE] ?? '',
            self::F_NON_PROD_MESSAGE_TITLE => $this->data[self::F_NON_PROD_MESSAGE_TITLE] ?? '',
            self::F_SEND_NON_INDEX_HEADERS => $this->data[self::F_SEND_NON_INDEX_HEADERS] ?? false,
        ];
    }

    public function getHostnames() : array
    {
        static $hostnames;
        if (!is_array($hostnames)) {
            $hostnames = explode(",", $this->data[self::F_PROD_HOSTNAMES] ?? '');
        }
        return $hostnames;
    }

    public function sendNonIdexHeader() : bool
    {
        return $this->data[self::F_SEND_NON_INDEX_HEADERS] ?? false;
    }

    public function getNonProdMessage() : string
    {
        return $this->data[self::F_NON_PROD_MESSAGE] ?? '';
    }

    public function getNonProdMessageTitle() : string
    {
        return $this->data[self::F_NON_PROD_MESSAGE_TITLE] ?? '';
    }

    public function onProdSystem() : bool
    {
        return in_array(gethostname(), $this->getHostnames());
    }

    public function getFields() : array
    {
        return [
            self::F_PROD_HOSTNAMES,
            self::F_NON_PROD_MESSAGE,
            self::F_NON_PROD_MESSAGE_TITLE,
            self::F_SEND_NON_INDEX_HEADERS
        ];
    }

}
