<?php

abstract class Core_Abstract_Page implements Core_Page_Interface {

    /**
     * @var array Query parameters
     */
    protected $request = array();

    /**
     * @var object GUI Builder
     */
    protected $builder = null;

    /**
     * @var string Name
     */
    protected $name = '';

    /**
     * Core_Abstract_Module_Page constructor.
     * @param array $query_args Query arguments necessary to page rendering
     */
    public function __construct($query_args = array())
    {
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
}
