<?php
namespace Arkitecht\SlackBot;


class SlackBotMessage
{
    private $keys = [
        'channel',
        'username',
        'text',
        'icon_emoji',
        'mrkdwn'
    ];
    private $message = [];

    public function __construct($text = '')
    {
        if ($text)
            $this->text = $text;
    }

    public function attach(SlackBotAttachment $attachment)
    {
        if (!array_key_exists('_attachments', $this->message))
            $this->message['_attachments'] = [];
        $this->message['_attachments'][] = $attachment;
    }

    public function setData($data)
    {
        foreach ($data as $key => $val)
            $this->$key = $val;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->message))
            return $this->message[$name];

        return null;
    }

    public function __set($name, $value)
    {
        if (!in_array($name, $this->keys))
            throw new \Exception("$name is not a valid message key");

        $this->message[$name] = $value;
    }

    public function __toJson()
    {
        $data = $this->message;

        if (array_key_exists('_attachments', $data)) {
            $data['attachments'] = [];
            foreach ($data['_attachments'] as $attachment)
                $data['attachments'][] = $attachment->asJson();
            unset($data['_attachments']);
        }

        return json_encode($data);
    }
}