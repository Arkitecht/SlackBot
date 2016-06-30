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
        'image_url',
        'thumb_url',
        'footer',
        'footer_icon',
        'ts',
        'mrkdwn_in'
    ];
    private $attachment = [];

    public function __construct($fallback)
    {
        $this->fallback = $fallback;
    }

    public function setData($data)
    {
        foreach ($data as $key => $val)
            $this->$key = $val;
    }

    public function addField($fieldOrTitle,$value='',$short=false)
    {
        if (is_a($fieldOrTitle,'\Arkitecht\SlackBot\SlackBotAttachmentField'))
            $this->_addField($fieldOrTitle);
        else {
            if ( !$value ) throw new \Exception("You must provide a value for SlackBotAttachmentField");
            $this->_addField(new SlackBotAttachmentField($fieldOrTitle,$value,$short));
        }
    }

    private function _addField(SlackBotAttachmentField $field)
    {
        if (!array_key_exists('_fields', $this->attachment))
            $this->attachment['_fields'] = [];
        $this->attachment['_fields'][] = $field;
    }


    public function setTimestamp($ts = '')
    {
        if (!$ts) $ts = time();
        $this->ts = $ts;
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

    public function asJson()
    {
        $data = $this->attachment;

        if (array_key_exists('_fields', $data)) {
            $data['fields'] = [];
            foreach ($data['_fields'] as $field)
                $data['fields'][] = $field->asJson();
            unset($data['_fields']);
        }

        return $data;
    }

    public function __toJson()
    {
        return json_encode($this->asJson());
    }
}