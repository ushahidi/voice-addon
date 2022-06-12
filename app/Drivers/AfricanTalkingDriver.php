<?php

namespace App\Drivers;

use App\Messages\Outgoing\LastScreen;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class AfricanTalkingDriver
{
    // Set your app credentials
    private  $username = 'sandbox';
    private $apikey   = 'de91fcb752e44a75f7089386cdb9c99b3d0866adb61154c97f3f40856f5460f7';

    /**
     * Build payload from incoming request.
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $data = $request->request->all();

        $payload = [
            'driver' => 'web',
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

        $this->payload = $payload;
        $this->event = Collection::make(array_merge($data, $payload));
        $this->config = Collection::make($this->config->get('web', []));
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

        $reply = $sessionIsCompleted ? 'END ' : 'CON ';

        $reply .= implode("\n", $replies);

        return $reply;
    }
    /**
     * Returns true if incoming request matches the expected by this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        $africasTalkingKeys = ['isActive', 'sessionId', 'direction', 'callerNumber', 'destinationNumber'];

        foreach ($africasTalkingKeys as $key) {
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
            $message = $this->event->get('recordingUrl');
            $userId = $this->event->get('callerNumber');
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
        return new User();
    }

    /**
     * @param IncomingMessage $message
     * @return \BotMan\BotMan\Messages\Incoming\Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
        $response = $this->buildReply($this->$message);
        return Answer::create($message->getText())->setMessage($response);
    }

    /**
     * @param string|Question|OutgoingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return array
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        if (! $message instanceof Question && ! $message instanceof OutgoingMessage) {
            $this->errorMessage = 'Unsupported message type.';
            $this->replyStatusCode = 500;
        }
        return [
            'message' => $message,
            'additionalParameters' => $additionalParameters,
        ];
    }

    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload)
    {
        $response = $this->http->post($this->endpoint . 'send', [], $payload, [
//            "Authorization: Bearer {$this->config->get('token')}",
            'Content-Type: application/json',
            "apiKey: {$this->apikey}",
            'Accept: application/json',
        ], true);

        return $response;
    }
}
