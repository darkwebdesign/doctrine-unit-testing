<?php
/*
 * This file bootstraps the test environment.
 */
namespace DarkWebDesign\DoctrineUnitTesting;

error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    throw new \Exception('Can\'t find autoload.php. Did you install dependencies via composer?');
}

if ( ! file_exists(__DIR__ . '/Proxies') && ! mkdir(__DIR__ . '/Proxies')) {
    throw new \Exception("Could not create " . __DIR__."/Proxies Folder.");
}

if ( ! file_exists(__DIR__ . '/ORM/Proxy/generated') &&  ! mkdir(__DIR__ . '/ORM/Proxy/generated')) {
    throw new \Exception('Could not create ' . __DIR__ . '/ORM/Proxy/generated Folder.');
}
