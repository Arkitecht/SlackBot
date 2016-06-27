<?php
namespace Arkitecht\SlackBot;


class SlackBotFormatter
{

    public function line($text)
    {
        return "$text\n";
    }

    public static function bold($text)
    {
        return '*'.$text.'*';
    }

    public static function pre($text)
    {
        return '```'.$text.'```';
    }

    public static function strike($text)
    {
        return '~'.$text.'~';
    }

    public static function italics($text)
    {
        return '_'.$text.'_';
    }

    public static function quote($text)
    {
        return '>'.$text;
    }

    public static function code($text)
    {
        return '`'.$text.'`';
    }

    public static function table($data, $options = [], $headers = [])
    {
        $table = new SlackBotTableFormatter($data, $options, $headers);
        return $table->output();
    }
}