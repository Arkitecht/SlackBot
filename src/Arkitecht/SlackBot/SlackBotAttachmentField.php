<?php
namespace Arkitecht\SlackBot;


class SlackBotAttachmentField
{
    public $title;
    public $value;
    public $short;

    public function __construct($title,$value,$short=false)
    {
        $this->title = $title;
        $this->value = $value;
        $this->short = $short;
    }

    public function __toJson()
    {
        return json_encode($this);
    }
}