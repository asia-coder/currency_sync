<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-07
 * Time: 16:36
 */

define('APP_DIR', __DIR__);

require APP_DIR . '/vendor/autoload.php';

$updater = new \Currency\Command\CurrenciesUpdater();
$updater->run();
