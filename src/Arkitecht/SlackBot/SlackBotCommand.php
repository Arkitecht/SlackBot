<?php
namespace Arkitecht\SlackBot;


abstract class SlackBotCommand
{
    private $arguments = [];
    private $bot;
    protected $description = 'Description of this command';
    protected $signature = '';

    public function __construct($arguments, $bot)
    {
        $this->arguments = $arguments;
        $this->bot = $bot;
    }

    public static function handleCommand($arguments, SlackBot $bot)
    {
        $command = new static($arguments, $bot);
        $command->handle();
    }

    public function handle()
    {
        $this->respond("This command has not been built yet");
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getArgument($idx = 0)
    {
        if (!array_key_exists($idx, $this->arguments))
            return '';

        return $this->arguments[$idx];
    }

    public function getRequestInput($input)
    {
        return $this->bot->getRequestInput($input);
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function respond($text)
    {
        $this->bot->respond($text);
    }

    public function json($json)
    {
        $this->bot->json($json);
    }

    public function __toString()
    {
        return (($this->signature) ?: '') . ' - ' . $this->description;
    }

}