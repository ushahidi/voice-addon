<?php

use App\Drivers\AfricanTalkingDriver;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

// Load the driver(s) you want to use
DriverManager::loadDriver(AfricanTalkingDriver::class);

$botman = resolve('botman');

// Give the bot something to listen for.
$botman->hears('hello', function (BotMan $bot) {
    $bot->reply('Hello yourself.');
});

// Start listening
$botman->listen();
