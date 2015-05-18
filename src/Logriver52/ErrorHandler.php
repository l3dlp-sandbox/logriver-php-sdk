<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

/**
 * LogRiver error handler class
 *
 * A class to enable logging of runtime errors, exceptions and fatal errors.
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
class Logriver_ErrorHandler
{
    private $sender;
    private $previousExceptionHandler;
    private $previousErrorHandler;
    private static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    public function __construct(Logriver_Sender $senderObj)
    {
        $this->sender = $senderObj;
    }

    public function register($errHandlerErr = true, $errHandlerExc = true, $errHandlerFatalErr = true)
    {
        if ($errHandlerErr === true) {
            $this->registerErrorHandler();
        }
        if ($errHandlerExc === true) {
            $this->registerExceptionHandler();
        }
        if ($errHandlerFatalErr === true) {
            $this->registerFatalHandler();
        }
    }

    private function registerErrorHandler($callPrevious = true)
    {
        $prev = set_error_handler(array($this, 'handleError'));
        if ($callPrevious === true) {
            if ($prev !== null) {
                $this->previousErrorHandler = $prev;
            } else {
                $this->previousErrorHandler = true;
            }
        }
    }

    private function registerExceptionHandler($callPrevious = true)
    {
        $prev = set_exception_handler(array($this, 'handleException'));
        if ($callPrevious === true && $prev !== null) {
            $this->previousExceptionHandler = $prev;
        }
    }

    private function registerFatalHandler()
    {
        register_shutdown_function(array($this, 'handleFatalError'));
    }

    public function handleError($code, $message, $file = '', $line = 0, $context = array())
    {
        if (substr($message, 0, 10) != '[LogRiver]') {
            $this->sender->doCaptureElementDetails(3, $message, $file, $line, $code);
        }
        if ($this->previousErrorHandler === true) {
            return false;
        } elseif ($this->previousErrorHandler !== null) {
            return call_user_func($this->previousErrorHandler, $code, $message, $file, $line, $context);
        }
    }

    public function handleException(Exception $exc)
    {
        $this->sender->doCaptureElement(4, $exc);
        if ($this->previousExceptionHandler !== null) {
            call_user_func($this->previousExceptionHandler, $exc);
        }
    }

    public function handleFatalError()
    {
        $lastError = error_get_last();
        if ($lastError !== null && isset($lastError['type']) && in_array($lastError['type'], self::$fatalErrors)) {
            $this->sender->doCaptureElementDetails(
                5,
                $lastError['message'],
                $lastError['file'],
                $lastError['line'],
                $lastError['type']
            );
        }
    }
}
