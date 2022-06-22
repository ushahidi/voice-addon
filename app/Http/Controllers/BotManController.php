<?php

namespace App\Http\Controllers;

use App\Drivers\AfricanTalkingDriver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BotManController extends Controller
{
    public function handle(Request $request, Response $response) {
        error_log("******received a call request*****");
        $data = $request->getContent();

        error_log( $data ); //todo remove line

        $arrayIncomingRequestData = explode("&", $data);

        $payload = $this->getFieldsFromRequest($arrayIncomingRequestData);

        error_log( json_encode($payload)  );


        if ($payload['isActive']== "1")  {
            $handler = new AfricanTalkingDriver();
            $handler->buildResponseheaders($response);
            return $handler->buildReply($payload);
        } else {
            //end conversation
        }
        return "DONE";
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
