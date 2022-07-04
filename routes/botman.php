<?php

use App\Conversations\SurveyConversation;
use App\Drivers\AfricanTalkingDriver;
use BotMan\BotMan\Drivers\DriverManager;

// Load the driver(s) you want to use
DriverManager::loadDriver(AfricanTalkingDriver::class);

// Botman commands for testing
$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('survey', function ($bot) {
    $bot->startConversation(new SurveyConversation());
});
