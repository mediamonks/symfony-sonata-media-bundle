<?php

use Composer\Autoload\ClassLoader;
use MediaMonks\SonataMediaBundle\Tests\App\AppKernel;
use Symfony\Component\HttpFoundation\Request;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/../app/AppKernel.php';
$kernel = new AppKernel('prod', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
