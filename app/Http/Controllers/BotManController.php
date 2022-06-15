<?php

namespace App\Http\Controllers;

use App\Drivers\AfricanTalkingDriver;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle(Request $request) {

        $xmlData = $request->getContent();
        $data = $request->request->all();

        $xml = simplexml_load_string($xmlData);

        error_log($xml->isActive . ' read isActive from xml');
        error_log($xml->sessionId . ' read sessionid from xmll');

        error_log(json_encode($data));

        $payload = [
            'isActive' => isset($data['isActive']) ?? 0,
            'sessionId' => $data['sessionId'] ?? null,
            'direction' => $data['direction'] ?? null,
            'callerNumber' => $data['callerNumber'] ?? null,
            'destinationNumber' => $data['destinationNumber'] ?? null,
            'dtmfDigits' => $data['dtmfDigits'] ?? null,
            'recordingUrl' => $data['recordingUrl'] ?? null,
            'durationInSeconds' => $data['durationInSeconds'] ?? null,
            'currencyCode' => $data['currencyCode'] ?? null,
            'amount' => $data['amount'] ?? null,
        ];

        error_log( json_encode($payload) . ' ** read post form data from request' );


        if ($payload->get('isActive') == 1)  {
            $handler = new AfricanTalkingDriver();
            return "Hello Africa Talking";
//            return $handler->buildReply($payload);
        }
        return "Unrecognized request";
    }
}
