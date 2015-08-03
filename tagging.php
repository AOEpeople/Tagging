#!/usr/bin/env php
<?php

namespace AOE\Tagging;

require __DIR__ . '/vendor/autoload.php';

use AOE\Tagging\Command\GitCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new GitCommand());
$application->run();
