<?php

require __DIR__ . '/Functional/config/bootstrap.php';

VCR\VCR::configure()
       ->setCassettePath(__DIR__ . '/Functional/var/vcr-tapes/')
       ->setMode(VCR\VCR::MODE_NEW_EPISODES)
       ->enableLibraryHooks(['curl'])
       ->setStorage('json');
VCR\VCR::turnOn();