<?php

/**
 * Class BGP_Application_Exception
 * Handle exceptions that occur DURING the application runtime
 * Write events in the log file with Log4php
 */
class Core_Application_Exception extends Core_Exception
{
    /**
     * BGP_Exception constructor.
     *
     * @param object $obj Calling object (pass to the constructor using $this)
     * @param string $message Error message
     * @param int $uid User-Id that triggered the error, 0 by default (anonymous)
     * @param int $code Exception code, 0 by default
     * @param Exception|null $previous Previous exception if nested exception
     */
    public function __construct($obj, $message, $uid = 0, $code = 0, Exception $previous = null)
    {
        $method = debug_backtrace()[1]['function'];
        $class = get_class($obj);

        if (is_a($obj, 'Core_Abstract_Module')) {
            // Module
            $this->getErrorLogger($obj->getModuleName(), $class, $method, $uid)->error($message);
        } else {
            // Core framework
            $this->getErrorLogger('core', $class, $method, $uid)->error($message);
        }

        parent::__construct($code, $message, $previous);
    }

    /**
     * LOGGING Configuration
     * Apache Log4php configuration
     *
     * @link http://logging.apache.org/log4php/docs/configuration.html
     * @param string $logger
     * @param $class
     * @param $method
     * @param int $uid
     * @return Logger
     */
    public function getErrorLogger($logger, $class, $method, $uid ) {

        // Configure logging
        Logger::configure(
            array(
                'rootLogger' => array(
                    'appenders' => array('default')
                ),
                'appenders' => array(
                    'default' => array(
                        'class' => 'LoggerAppenderFile',
                        'layout' => array(
                            'class' => 'LoggerLayoutPattern',
                            'params' => array(
                                'conversionPattern' => '[%date{Y-m-d H:i:s,u}] %-5level %-10.10logger ' .
                                    $uid . ' '
                                    . '%-15.15server{REMOTE_ADDR} '
                                    . '%-35server{REQUEST_URI} '
                                    . '"%msg" '
                                    . '' . $class . ' '
                                    . '' . $method . ' '
                                    . '%n'
                            )
                        ),
                        'params' => array(
                            'file' => REAL_LOGGING_DIR . '/' . date('Y-m-d') . '.txt',
                            'append' => true
                        )
                    )
                )
            )
        );

        return Logger::getLogger( $logger );
    }
}
