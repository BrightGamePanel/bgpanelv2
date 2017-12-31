<?php

abstract class Core_Abstract_Page implements Core_Page_Interface {

    /**
     * @var Core_Module_Interface Parent module
     */
    protected $parent_module = null;

    /**
     * @var Core_Page_Interface Parent page
     */
    protected $parent_page = null;

    /**
     * @var array Query parameters
     */
    protected $request = array();

    /**
     * @var Core_Page_Builder GUI Builder
     */
    protected $builder = null;

    /**
     * @var string Title
     */
    protected $title = '';

    /**
     * Core_Abstract_Module_Page constructor.
     * @param Core_Module_Interface $parent_module Attached parent module
     * @param Core_Page_Interface $parent_page Parent page (if any)
     * @param array $query_args Query arguments necessary to page rendering
     */
    public function __construct($parent_module, $parent_page = null, $query_args = array())
    {
        $this->parent_module = $parent_module;
        $this->parent_page = $parent_page;

        $this->request = $query_args;
        $this->builder = new Core_Page_Builder($this);

        $this->title = str_replace('_Page', '', get_class($this));
        $this->title = str_replace('_', ' ', $this->title);
    }

    public function renderPage() {

        // Build Page Header
        $this->builder->buildHeader();

        // Render Page Body
        $this->body();

        // Build Page Footer
        $this->builder->buildFooter();
    }

    public function getPageTitle() {
        return $this->title;
    }

    public function getParentPage()
    {
        return $this->parent_page;
    }

    public function getModuleTitle()
    {
        return $this->parent_module->getModuleTitle();
    }

    public function getStylesheets()
    {
        return $this->parent_module->getStylesheets();
    }

    public function getJavascript()
    {
        return $this->parent_module->getJavascript();
    }

    public function getOptions()
    {
        return $this->parent_module->getOptions();
    }

    public function getIcon()
    {
        return $this->parent_module->getIcon();
    }
}
