<?php

namespace App\Drivers;

use App\Messages\Outgoing\LastScreen;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AfricanTalkingDriver
{
    // Set your app credentials
    private  $username = 'sandbox';
    private $apikey   = 'de91fcb752e44a75f7089386cdb9c99b3d0866adb61154c97f3f40856f5460f7';

    /**
     * Build payload from incoming request.
     * @param Request $request
     */
    public function buildReply($payload)
    {
        $text = "Sorry, your phone number is not registered for this service. Good Bye.";

        // Compose the response
        $response  = '<?xml version="1.0" encoding="UTF-8"?>';
        $response .= '<Response>';
        $response .= '<Say>'.$text.'</Say>';
        $response .= '</Response>';

        return $response;
    }

    public function buildResponseheaders(Response $response) {
        $response->withHeaders([
            'Content-type' => 'text/plain',
            'apiKey' => this->$apikey
        ]);
    }


}
