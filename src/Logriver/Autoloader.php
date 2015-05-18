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

/**
 * LogRiver autoloader class
 *
 * A class to load files automatically with recently version of PHP (5.3+).
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */
class LogriverAutoloader
{

    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array('Logriver\\LogriverAutoloader', 'loadClass'));
    }

    public static function loadClass($className)
    {
        // If last Php version is used with the automatic loader (if user doesn't know the Php version)
        if (strpos($className, 'Logriver_') === 0) {
            require_once dirname(__FILE__) . '/../LogriverAuto/Autoloader.php';
            return \LogriverAuto_Autoloader::loadClass($className);
        } elseif (strpos($className, 'Logriver\\') === 0) {
            $file = dirname(__FILE__) . '/../' . str_replace(
                array("\0", '\\'),
                array('', DIRECTORY_SEPARATOR),
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
