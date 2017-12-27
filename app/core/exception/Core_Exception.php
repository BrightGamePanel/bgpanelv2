<?php

/**
 * Class Core_Exception
 * Handle exceptions that occur BEFORE the application runtime
 * Display the error with a minimalistic style
 */
class Core_Exception extends BGP_Exception {

    protected $h1;
    protected $h3;

    /**
     * Core_Exception constructor.
     *
     * @param string $h1
     * @param string $h3
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($h1, $h3, $message, $code = 500, Exception $previous = null)
    {
        $this->h1 = $h1;
        $this->h3 = $h3;

        parent::__construct($code, $message, $previous);
    }

    public function __toString() {
        return '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>' . htmlspecialchars($this->h1, ENT_QUOTES) . '</h1><br />
        <h3>' . htmlspecialchars($this->h3, ENT_QUOTES) . '</h3>
        <p>' . htmlspecialchars($this->getMessage(), ENT_QUOTES) . '</p>
    </body>
</html>
        ';
    }
}
