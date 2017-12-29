<?php

abstract class Core_Abstract_Page implements Core_Page_Interface {

    /**
     * @var Core_Module_Interface Parent module
     */
    protected $parent_module = null;

    /**
     * @var array Query parameters
     */
    protected $request = array();

    /**
     * @var Core_Page_Builder GUI Builder
     */
    protected $builder = null;

    /**
     * @var string Name
     */
    protected $name = '';

    /**
     * Core_Abstract_Module_Page constructor.
     * @param Core_Module_Interface $parent_module Attached parent module
     * @param array $query_args Query arguments necessary to page rendering
     */
    public function __construct($parent_module, $query_args = array())
    {
        $this->parent_module = $parent_module;
        $this->request = $query_args;
        $this->builder = new Core_Page_Builder($this);
        $this->name = get_class($this);
    }

    public function render() {

        // Build Page Header
        $this->builder->buildHeader();

        // Render Page Body
        $this->body();

        // Build Page Footer
        $this->builder->buildFooter();
    }

    public function getModuleTitle()
    {
        return $this->parent_module->getTitle();
    }
}
