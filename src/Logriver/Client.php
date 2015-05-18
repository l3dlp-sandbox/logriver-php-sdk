<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

namespace Logriver;

use Logriver\Sender as Sender;

/**
 * LogRiver client class
 *
 * It contains the methods to use on websites, web applications or scripts.
 * They send messages, errors and exceptions to LogRiver.
 *
 * Initialize with Composer:
 *     require '/my_lib_path/logriver-php/vendor/autoload.php';
 *     \Logriver\Client::init('[your_api_key]')->startListener();
 *
 * Initialize with source:
 *     require '/my_lib_path/logriver-php/src/Logriver/Autoloader.php';
 *     \Logriver\LogriverAutoloader::register();
 *     \Logriver\Client::init('[your_api_key]')->startListener();
 *
 * Usage:
 *     \Logriver\Client::captureEvent("My message");
 *     \Logriver\Client::captureError("An error");
 *     \Logriver\Client::captureException("An exception");
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
class Client extends Sender
{
    private static $instance = null;

    public static function init($apiKey = null)
    {
        if (is_null(self::$instance)) {
            if (!defined('_DEF_LOGRIVER_API_KEY')) {
                define('_DEF_LOGRIVER_API_KEY', $apiKey);
            }
            if ($apiKey === null) {
                throw new \Exception("apiKey parameter is required");
            } elseif (!is_string($apiKey) || (is_string($apiKey) && strlen($apiKey) !== 32)) {
                throw new \Exception("apiKey parameter is not correct");
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

    public static function captureEvent($message, $mixedData = null)
    {
        if (self::$instance === null && defined('_DEF_LOGRIVER_API_KEY')) {
            trigger_error("LogRiver Error: You must use Logriver_Client instead Logriver\Client", E_USER_ERROR);
        }
        self::$instance->doCaptureElement(1, $message, $mixedData);
    }

    public static function captureMessage($message, $mixedData = null)
    {
        if (self::$instance === null && defined('_DEF_LOGRIVER_API_KEY')) {
            trigger_error("LogRiver Error: You must use Logriver_Client instead Logriver\Client", E_USER_ERROR);
        }
        self::$instance->doCaptureElement(2, $message, $mixedData);
    }

    public static function captureError($messageOrObject, $mixedData = null)
    {
        if (self::$instance === null && defined('_DEF_LOGRIVER_API_KEY')) {
            trigger_error("LogRiver Error: You must use Logriver_Client instead Logriver\Client", E_USER_ERROR);
        }
        self::$instance->doCaptureElement(3, $messageOrObject, $mixedData);
    }

    public static function captureException($messageOrObject, $mixedData = null)
    {
        if (self::$instance === null && defined('_DEF_LOGRIVER_API_KEY')) {
            trigger_error("LogRiver Error: You must use Logriver_Client instead Logriver\Client", E_USER_ERROR);
        }
        self::$instance->doCaptureElement(4, $messageOrObject, $mixedData);
    }
}
