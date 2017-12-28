<?php

class Core_Page_Builder {

    /**
     * @var object Page handle
     */
    private $page = null;

    /**
     * Core_Page_Builder constructor.
     * @param object $page The page to build
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    public function buildHeader() {
        // TODO : implement
    }

    public function buildFooter() {
        // TODO : implement
    }
}