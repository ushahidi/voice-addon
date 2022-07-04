<?php

namespace App\Http\Controllers;

use App\Conversations\ExampleConversation;
use App\Drivers\AfricanTalkingDriver;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php.
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    function getFieldsFromRequest(array $arrayIncomingRequestData){
        $i = 0;
        $payload = [];
        while($i < count($arrayIncomingRequestData))
        {
            $keyValueArr = explode("=", $arrayIncomingRequestData[$i]);
            $payload[$keyValueArr[0] . ''] = $keyValueArr[1];
            $i++;
        }

        return $payload;
    }
}
