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
 * LogRiver autoloader class
 *
 * A class to load files automatically with recently version of PHP (5.2+).
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 29, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
class LogriverAuto_Autoloader
{

    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array('LogriverAuto_Autoloader', 'loadClass'));
    }

    public static function loadClass($className)
    {
        if (strpos($className, 'Logriver_') === 0) {
            $boom = explode('Logriver_', $className);
            $className = 'Logriver52_' . $boom[1];
            $file = dirname(__FILE__) . '/../' . str_replace(
                array('_', "\0"),
                array(DIRECTORY_SEPARATOR, ''),
                $className
            ) . '.php';
            if (is_file($file)) {
                require $file;
                return true;
            }
        }
        return null;
    }
}
