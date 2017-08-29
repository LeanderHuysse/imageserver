<?php

namespace IOLabs\Helper;

use IOLabs\Config as Config;

class SourceHelper
{

    protected $source;

    /**
     * SourceHelper constructor.
     */
    public function __construct()
    {
        $this->config = new Config();
        $this->source = $this->config->getSetting('directory');
    }

    /**
     * @param $src
     *
     * @return bool
     */
    public function sourceExists(string $src): bool
    {
        $results = glob("{$this->source}/$src*");
        if(count($results) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $src
     *
     * @return array
     */
    public function getSources(string $src): array
    {
        $results = glob("{$this->source}/$src*");
        return $results;
    }
}