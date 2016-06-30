<?php
namespace Arkitecht\SlackBot;

use GuzzleHttp\Client;

class SlackBotHook
{
    public $hook;
    private $client;

    public function __construct($hook)
    {
        $this->hook = $hook;
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function postText($text)
    {
        return $this->post(json_encode(['text' => $text]));
    }

    public function post($body)
    {
        return $this->client->post($this->hook, [
            'body' => $body
        ]);
    }

    public function postMessage(SlackBotMessage $message)
    {
        return $this->post($message->__toJson());
    }
}