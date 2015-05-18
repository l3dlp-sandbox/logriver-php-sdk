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
 * LogRiver client class
 *
 * It contains the methods to use on websites, web applications or scripts.
 * They send messages, errors and exceptions to LogRiver.
 *
 * Usage:
 *     require '/my_lib_path/logriver-php/src/LogriverAuto/Autoloader.php';
 *     LogriverAuto_Autoloader::register();
 *     Logriver_Client::init('[your_api_key]')->startListener();
 *
 *     Logriver_Client::captureEvent("My message");
 *     Logriver_Client::captureError("An error");
 *     Logriver_Client::captureException("An exception");
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
class Logriver_Client extends Logriver_Sender
{
    private static $instance = null;

    public static function init($apiKey = null)
    {
        if (is_null(self::$instance)) {
            if (!defined('_DEF_LOGRIVER_API_KEY')) {
                define('_DEF_LOGRIVER_API_KEY', $apiKey);
                require dirname(__FILE__) . '/Global.php'; // __DIR__ appears only in Php 5.3
            }
            if ($apiKey === null) {
                throw new Exception("apiKey parameter is required");
            } elseif (!is_string($apiKey) || (is_string($apiKey) && strlen($apiKey) !== 32)) {
                throw new Exception("apiKey parameter is not correct");
            }
            self::$instance = new self($apiKey);
        }
        return self::$instance;
    }

    public function ignoreError()
    {
        $this->doIgnoreError();
    }

    public function ignoreException()
    {
        $this->doIgnoreException();
    }

    public function ignoreFatalError()
    {
        $this->doIgnoreException();
    }

    public function startListener()
    {
        $this->doStartListener();
    }

    public static function captureEvent($message)
    {
        self::$instance->doCaptureElement(1, $message);
    }

    public static function captureMessage($message, $mixedData = null)
    {
        self::$instance->doCaptureElement(2, $message, $mixedData);
    }

    public static function captureError($messageOrObject, $mixedData = null)
    {
        self::$instance->doCaptureElement(3, $messageOrObject, $mixedData);
    }

    public static function captureException($messageOrObject, $mixedData = null)
    {
        self::$instance->doCaptureElement(4, $messageOrObject, $mixedData);
    }
}
