#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
use Danj\Spotify\Application;

$app = new Application();

$app->start([
    Danj\Spotify\Commands\Auth::class,
    Danj\Spotify\Commands\Playback\Pause::class,
    Danj\Spotify\Commands\Playback\Play::class,
    Danj\Spotify\Commands\Playback\Next::class,
    Danj\Spotify\Commands\Playback\Previous::class,
    Danj\Spotify\Commands\Playback\Volume::class

]);

$application->run();
