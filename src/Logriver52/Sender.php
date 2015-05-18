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
 * LogRiver sender class
 *
 * A class to send data to LogRiver's servers.
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
abstract class Logriver_Sender
{
    const HOST = 'dHVydGxlLWxpc3RlbmVyLmxvZ3JpdmVyLmlv';
    const VERSION = '0.1.1';
    private $debug = false;
    private $debugTerm = false;
    private $debugModel = null;
    private $stb;
    private $apiKey;
    private $callId;
    private $errorHandler;
    private $errorHandlerError = true;
    private $errorHandlerException = true;
    private $errorHandlerFatalError = true;
    private $timeStart = 0;

    protected function __construct($apiKey)
    {
        $this->timeStart = time();
        $this->stb = self::getMt();
        $this->apiKey = $apiKey;
        $this->isInstalled();
        $this->callId = md5(uniqid(rand(), true));
    }

    public function setDebugMode($param = false) {
        $this->debug = true;
        if($param === 1) {
            $this->debugTerm = true;
        }
    }

    public function getDebugModel() {
        return $this->debugModel;
    }

    private static function getMt()
    {
        return microtime(true);
    }

    protected function doIgnoreError()
    {
        $this->errorHandlerError = false;
    }

    protected function doIgnoreException()
    {
        $this->errorHandlerException = false;
    }

    protected function doIgnoreFatalError()
    {
        $this->errorHandlerFatalError = false;
    }

    protected function doStartListener()
    {
        $this->errorHandler = new Logriver_ErrorHandler($this);
        $this->errorHandler->register(
            $this->errorHandlerError,
            $this->errorHandlerException,
            $this->errorHandlerFatalError
        );
        // Calling here to be used after register_shutdown_function handleFatalError
        register_shutdown_function(array($this, 'getLoadTime'));
    }

    public function doCaptureElement($type, $messageOrObject, $mixedData = null)
    {
        $model = new Logriver_Model();
        if ($this->errorHandler === null) {
            throw new Exception('You must use "$client->startListener();" just after "LogriverAuto_Autoloader::register();"');
        }
        if ($type >= 1 && $type <= 5) {
            $model->type = $type;
            $model->mt = self::getMt();
            $model->m = memory_get_usage(true) . ':' . memory_get_peak_usage(true);
            $model->st = $this->stb;
            if(is_object($messageOrObject) && method_exists($messageOrObject, 'getMessage')) {
                $model->file = $messageOrObject->getFile();
                $model->line = $messageOrObject->getLine();
                $model->message = $messageOrObject->getMessage();
                $model->ct = $messageOrObject->getCode();
                // Excluding events and messages because they doesn't need traces
                // Excluding fatal error because there no trace since the beginning of the execution
                if($type >= 3 && $type <= 4) {
                    $model->t = $messageOrObject->getTrace();
                    foreach($model->t as $key=>$value) {
                        unset($model->t[$key]['args']);
                    }
                    array_unshift($model->t, array('file'=>$model->file, 'line'=>$model->line));
                }
            }
            elseif(is_string($messageOrObject)) {
                $model->message = $messageOrObject;
                $traceFound = debug_backtrace(false); // DEBUG_BACKTRACE_IGNORE_ARGS doesn't exist before 5.3.6
                for($i=0;$i<=1;$i++) {
                    if (isset($traceFound[$i]['class']) && ($traceFound[$i]['class'] == 'Logriver_Sender')) {
                        unset($traceFound[$i]);
                    }
                }
                $model->file = $traceFound[key($traceFound)]['file'];
                $model->line = $traceFound[key($traceFound)]['line'];
                if ($type === 2) {
                    $model->ct = E_USER_NOTICE;
                } elseif ($type === 3) {
                    $model->ct = E_USER_WARNING;
                } elseif ($type === 4) {
                    $model->ct = E_USER_ERROR;
                }
                if ($type >= 3 && $type <= 4) {
                    $model->t = $traceFound;
                }
            }
            if (isset($_SERVER['SERVER_NAME'])) {
                $model->cat = 1;
            } elseif (isset($_SERVER['TERM'])) {
                $model->cat = 2;
            }
            $model->ds = $_SERVER;
            if($mixedData !== null) {
                $model->md = $mixedData;
            }
            $send = $model->doGenerateHttpParams();
        } else {
            $send = 'type=' . $type;
            if(is_float($messageOrObject)) {
                $send .= '&data=' . $messageOrObject;
            }
        }
        if($this->debug === false) {
            $this->doSendData($send);
        }
        else {
            $this->debugModel = $model;
            if($this->debugTerm === true) {
                echo @serialize($this->debugModel);
                exit();
            }
        }
    }

    public function doCaptureElementDetails($type, $message, $file = null, $line = null, $codeType = null) {
        $model = new Logriver_Model();
        if ($this->errorHandler === null) {
            throw new Exception('You must use "$client->startListener();" just after "LogriverAuto_Autoloader::register();"');
        }
        if ($type >= 1 && $type <= 5) {
            $model->type = $type;
            $model->mt = self::getMt();
            $model->m = memory_get_usage(true) . ':' . memory_get_peak_usage(true);
            $model->st = $this->stb;
            $model->message = $message;
            $model->line = $line;
            $model->file = $file;
            // Uniquement pour Error et Exception
            if ($type >= 3 && $type <= 4) {
                $model->t = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                for ($i = 0; $i <= 1; $i++) {
                    if (isset($model->t[$i]['class']) && ($model->t[$i]['class'] == 'Logriver\Sender' || $model->t[$i]['class'] == 'Logriver_Sender' || $model->t[$i]['class'] == 'Logriver\ErrorHandler' || $model->t[$i]['class'] == 'Logriver_ErrorHandler')) {
                        unset($model->t[$i]);
                    }
                }
                array_unshift($model->t, array('file' => $model->file, 'line' => $model->line));
            }
            $model->ct = $codeType;
            if (isset($_SERVER['SERVER_NAME'])) {
                $model->cat = 1;
            } elseif (isset($_SERVER['TERM'])) {
                $model->cat = 2;
            }
            $model->ds = $_SERVER;

            $send = $model->doGenerateHttpParams();
        } else {
            $send = 'type=' . $type;
            $send .= '&data=' . $message;
        }
        if($this->debug === false) {
            $this->doSendData($send);
        }
        else {
            $this->debugModel = $model;
            if($this->debugTerm === true) {
                echo @serialize($this->debugModel);
                exit();
            }
        }
    }

    private function doSendData($data)
    {
        $data .= '&ak=' . $this->apiKey;
        if (!isset($_SERVER['TERM'])) {
            $data .= '&url=' . urlencode(self::getUrl());
        }
        $data .= '&ci=' . $this->callId . '&la=1';
        $host = base64_decode(self::HOST);
        $header = '';
        $header .= 'POST /traxer/add/ HTTP/1.0' . "\r\n";
        $header .= 'Host: ' . $host . "\r\n";
        $header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
        $header .= 'Content-Length: ' . strlen($data) . "\r\n\r\n";
        $errno = null;
        $errstr = null;
        $socket = @fsockopen('ssl://' . $host, 443, $errno, $errstr, 3);
        if ($socket !== false) {
            @stream_set_blocking($socket, false);
            fwrite($socket, $header . $data);
            fclose($socket);
        } else {
            if (function_exists('curl_init')) {
                $url = 'ssl://' . $host . ':443/traxer/add/';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                if (preg_match('`^https://`i', $url)) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                }
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_exec($ch);
                curl_close($ch);
            } else {
                trigger_error(
                    "[LogRiver] " .
                    "LogRiver cannot send data because the library fsockopen or curl is not installed on your server",
                    E_USER_WARNING
                );
            }
        }
    }

    private static function getUrl()
    {
        $serv = $_SERVER;
        $ssl = (!empty($serv['HTTPS']) && $serv['HTTPS'] == 'on') ? true : false;
        $spr = strtolower($serv['SERVER_PROTOCOL']);
        $proto = substr($spr, 0, strpos($spr, '/')) . (($ssl) ? 's' : '');
        $port = $serv['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($serv['HTTP_X_FORWARDED_HOST']) ? $serv['HTTP_X_FORWARDED_HOST'] : isset($serv['HTTP_HOST']) ?
            $serv['HTTP_HOST'] : $serv['SERVER_NAME'];
        $url = $proto . '://' . $host . $port . $serv['REQUEST_URI'];
        return $url;
    }

    private function isInstalled()
    {
        if (isset($_GET['isos_' . $this->apiKey])) {
            header('Monitoring-LRV: ' . self::VERSION);
        }
    }

    public function getLoadTime()
    {
        $this->doCaptureElement(6, round(self::getMt() - $this->timeStart, 2));
    }
}
