<?php
namespace Arkitecht\SlackBot;


use Illuminate\Support\Collection;

abstract class SlackBotCommandChain extends SlackBotCommand
{
    private $commands;

    public function __construct($arguments, $bot)
    {
        parent::__construct($arguments, $bot);
        $this->commands = new Collection();
    }

    public function addCommand($command)
    {
        $this->commands->push($command);
    }

    public static function handleCommand($arguments, SlackBot $bot)
    {
        $commandChain = new static($arguments,$bot);
        $commandChain->setCommands();
        $commandChain->commands->each(function($command) use ($arguments,$bot) {
            $command::handleCommand($arguments,$bot);
        });
    }

}