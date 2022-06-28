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
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function buildPayload(Request $request)
    {
        $body = $request->getContent();

        error_log( $body ); //todo remove line

        $arrayIncomingRequestData = explode("&", $body);

        $data = $this->getFieldsFromRequest($arrayIncomingRequestData);

        $payload = [
            'driver'  => 'web',
            'message' => $payload['recordingUrl'] ?? null,
            'userId'  => $payload['callerNumber'] ?? null
        ];

        $this->payload = $payload;
        $this->event = Collection::make(array_merge($data, $payload));
        $this->files = Collection::make($request->files->all());
        $this->config = Collection::make($this->config->get('web', []));
    }

    /**
     * Returns true if incoming request matches the expected by this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        $africasTalkingVoiceKeys = ['sessionId', 'callerNumber', 'callerCountryCode', 'isActive', 'direction'];

        foreach ($africasTalkingVoiceKeys as $key) {
            if (is_null($this->event->get($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages()
    {
        if (empty($this->messages)) {
            $message = $this->event->get('message');
            $userId = $this->event->get('userId');
            $this->messages = [new IncomingMessage($message, $userId, $userId, $this->payload)];
        }
        return $this->messages;
    }

    /**
     * Retrieve User information.
     * @param IncomingMessage $matchingMessage
     * @return User
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        return new User($matchingMessage->getSender());
    }

    /**
     * Take outgoing messages from Botman and build payload for the service.
     *
     * @param string|Question|OutgoingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     *
     * @return string|Question|OutgoingMessage
     */
    public function buildServicePayload($message, IncomingMessage $matchingMessage, array $additionalParameters = [])
    {
        if (! $message instanceof Question && ! $message instanceof OutgoingMessage) {
            $this->errorMessage = 'Unsupported message type.';
            $this->replyStatusCode = 500;
        }

        return $message;
    }

    /**
     * Take all the outgoing messages and build a reply understandable by
     * Africa's Talking USSD gateway.
     * @param array $messages
     * @return string|null
     */
    protected function buildReply($messages)
    {
        if (empty($messages)) {
            return;
        }

        $sessionIsCompleted = false;
        $replies = [];

        foreach ($messages as $message) {
            if ($message instanceof OutgoingMessage || $message instanceof Question) {
                $replies[] = $message->getText();
            }

            if ($message instanceof LastScreen && ! $message->getCurrentPage()->hasNext()) {
                $sessionIsCompleted = true;
            }
        }

        error_log($replies);
        $reply = $sessionIsCompleted ? 'END ' : 'CON ';

        $reply .= implode("\n", $replies);

        return $reply;
    }

    /**
     * Send out message response.
     */
    public function messagesHandled()
    {
        $response = $this->buildReply($this->replies);

        if ($response) {
            // Reset replies
            $this->replies = [];

            \Symfony\Component\HttpFoundation\Response::create($response, $this->replyStatusCode, ['Content-Type' => 'text/plain'])->send();
        } else {
            Response::create(null, Response::HTTP_CONFLICT)->send();
        }
    }
//    /**
//     * Build payload from incoming request.
//     * @param Request $request
//     */
//    public function buildReply($payload)
//    {
//        $text = "Welcome to Ushahidi Platform survey";
//
//        // Compose the response
/*        $response  = '<?xml version="1.0" encoding="UTF-8"?>';*/
//        $response .= '<Response>';
//        $response .= '<Say>'.$text.'</Say>';
//        $response .= '</Response>';
//
//        return $response;
//    }

    public function buildResponseheaders(Response $response) {
        $response->withHeaders([
            'Content-type' => 'text/plain',
            'apiKey' => $this->apikey
        ]);
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
