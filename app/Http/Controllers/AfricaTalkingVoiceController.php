<?php

namespace App\Http\Controllers;

use App\Conversations\SurveyConversation;
use Illuminate\Support\Facades\Log;

class AfricaTalkingVoiceController extends Controller
{
    /**
     * Handle each request from Africa's Talking USSD gateway.
     *
     * @return void
     */
    public function handle()
    {
        error_log("******received a call request*****");
        $botman = app('botman');

        /*
         * If the message sent by the AT gateway didn't match any command
         * or previous conversation, start a new survey conversation.
         *
         * We can not make USSD providers send specific Botman commands,
         * so we always fallback to starting the survey conversation, once
         * the conversation is started all incoming messages are handled from there.
         */
        $botman->fallback(function ($bot) {
            error_log("******starting a conversation*****");
            $convo = new SurveyConversation();


            error_log("******got convo *****");

            // Store the user's phone number
            $from = $bot->getMessage()->getPayload()['userId'] ?? '+0000000';
            $convo->setUserId($from);

            // Kick off the conversaition
            $bot->startConversation($convo);
        });

        try {
            error_log("******listening for request*****");
            $botman->listen();
        } catch (\Exception $e) {
            error_log("******error occured*****");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            error_log($e->getTrace());
            Log::error($e->getMessage());
            report($e);
            $botman->reply('Something went wrong.');
        }

    }
}
