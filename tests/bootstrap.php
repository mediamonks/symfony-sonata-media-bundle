<?php

$file = __DIR__ . '/../vendor/autoload.php';

VCR\VCR::configure()
       ->setCassettePath(__DIR__ . '/Functional/var/vcr-tapes/')
       ->setMode(VCR\VCR::MODE_NEW_EPISODES)
       ->enableLibraryHooks(['curl'])
       ->setStorage('json');
VCR\VCR::turnOn();

if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies using composer to run the test suite.');
}
$autoload = require $file;
