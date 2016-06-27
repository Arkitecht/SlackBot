<?php
namespace Arkitecht\SlackBot;


class SlackBotAttachment
{
    private $keys = [
        'fallback',
        'color',
        'pretext',
        'author_name',
        'author_link',
        'author_icon',
        'title',
        'title_link',
        'text',
        'fields',
        'image_url',
        'thumb_url',
        'footer',
        'footer_icon',
        'ts'
    ];
    private $attachment = [];

    public function __construct($fallback)
    {
        $this->fallback = $fallback;
    }

    public function setTimestamp($ts = '')
    {
        if (!$ts) $ts = time();
        $this->timestamp = $ts;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attachment))
            return $this->attachment[$name];

        return null;
    }

    public function __set($name, $value)
    {
        if (!in_array($name, $this->keys))
            throw new \Exception("$name is not a valid attachment key");

        $this->attachment[$name] = $value;
    }


}