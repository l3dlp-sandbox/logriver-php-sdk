<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

error_reporting(E_ALL | E_STRICT);

if (version_compare(phpversion(), '5.3', '>=')) {
    if(file_exists(__DIR__ . '/../vendor/autoload.php')) {
        $loader = require __DIR__ . '/../vendor/autoload.php';
        $loader->add('\\Logriver\\', __DIR__);
    }
    else {
        require dirname(__FILE__) . '/../src/Logriver/Autoloader.php';
        \Logriver\LogriverAutoloader::register();
    }
} else {
    require dirname(__FILE__) . '/../src/LogriverAuto/Autoloader.php';
    LogriverAuto_Autoloader::register();
}
