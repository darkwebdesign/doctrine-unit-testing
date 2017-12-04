<?php
/*
 * This file bootstraps the test environment.
 */
namespace DarkWebDesign\DoctrineUnitTesting;

error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // dependencies were installed via composer - this is the main project
    require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    // installed as a dependency in `vendor`
    require __DIR__ . '/../../../autoload.php';
} else {
    throw new \Exception('Can\'t find autoload.php. Did you install dependencies via composer?');
}

if ( ! file_exists(__DIR__ . '/Proxies') && ! mkdir(__DIR__ . '/Proxies')) {
    throw new \Exception("Could not create " . __DIR__."/Proxies Folder.");
}

if ( ! file_exists(__DIR__ . '/ORM/Proxy/generated') &&  ! mkdir(__DIR__ . '/ORM/Proxy/generated', 0777, true)) {
    throw new \Exception('Could not create ' . __DIR__ . '/ORM/Proxy/generated Folder.');
}
