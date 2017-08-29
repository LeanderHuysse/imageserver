<?php

namespace IOLabs;

class Config
{
    public function __construct()
    {
        $this->directory = $_SERVER['DOCUMENT_ROOT'] . '/data';
    }

    public function getSetting($request)
    {
        if($this->$request) {
            return $this->$request;
        }
    }
}