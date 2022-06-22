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
    private  $username = 'Intern';
    private $apikey   = '2525d2eaabaf067a6b2a702284b3eb7477a9a2c68393fec3ab9b87915629e617';

    /**
     * Build payload from incoming request.
     * @param Request $request
     */
    public function buildReply($payload)
    {
        $text = "Welcome to Ushahidi Platform survey";

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
            'apiKey' => $this->apikey
        ]);
    }


}
