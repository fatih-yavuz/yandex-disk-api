<?php
/**
 * Created by PhpStorm.
 * User: fatih
 * Date: 21/03/2017
 * Time: 09:51
 */

require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('yandex_disk');
$log->pushHandler(new StreamHandler('disk.log', Logger::WARNING));

// add records to the log
//$log->warning('Foo');
//$log->error('Bar');