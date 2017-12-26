<?php

class BGP_Exception extends Exception
{
    /**
     * Core_Exception constructor.
     * @param string $message Error message
     * @param int $uid User-Id that triggered the error, 0 by default (anonymous)
     * @param int $code Exception code, 0 by default
     * @param Exception|null $previous Previous exception if nested exception
     */
    public function __construct($message, $uid = 0, $code = 0, Exception $previous = null)
    {
        $class  = debug_backtrace()[1]['class'];
        $method = debug_backtrace()[1]['function'];

        $obj = new $class();
        if (is_a($obj, 'BGP_Module')) {
            // Module
            $this->getErrorLogger($obj->getModuleName(), $class, $method, $uid)->error($message);
        } else {
            // Core framework
            $this->getErrorLogger('core', $class, $method, $uid)->error($message);
        }

        parent::__construct($message, $code, $previous);
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

class Core_ApplicationNotInstalled_Exception extends BGP_Exception {

}

class Core_AuthService_Exception extends BGP_Exception {

}

class Core_Core_AuthService_JWT_Exception extends Core_AuthService_Exception {

}

class Core_BGP_Module_Exception extends BGP_Exception {

}
