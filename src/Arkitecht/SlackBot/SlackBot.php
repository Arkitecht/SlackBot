<?php
namespace Arkitecht\SlackBot;

use Arkitecht\SlackBot\SlackBotCommand;

class SlackBot
{

    private $request;
    private $commands;
    private $permissions;

    public function __construct()
    {
        $this->request = \Illuminate\Http\Request::capture();
        $this->commands = [];
    }

    public function addCommand($key, $command)
    {
        $this->commands[$key] = $command;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    public function setPermissions(SlackBotPermissions $permissions)
    {
        $permissions->setTarget($this);
        $this->permissions = $permissions;
    }

    public function getRequestInput($input)
    {
        return $this->request->input($input);
    }

    public function handle()
    {
        if ($this->permissions) {
            $hasPerms = $this->permissions->checkAccess();
            if (!$hasPerms) return false;
        }
        $pieces = $this->parsePieces($this->request->input('text'));
        if (!is_array($pieces))
            $command = '';
        else
            $command = array_shift($pieces);

        $this->handleCommand($command, $pieces);

        return true;
    }

    public function handleCommand($command, $arguments)
    {
        if (!array_key_exists($command, $this->commands)) {
            if (array_key_exists('help', $this->commands)) {
                if ( $command ) $this->respond(':exclamation: '.$command . ' - *command does not exist*');
                $this->handleCommand('help', $arguments);

                return;
            } else
                die('command not found and no default');
        }

        $commandClass = $this->commands[$command];
        $commandClass::handleCommand($arguments, $this);
    }

    private function parsePieces($commandText)
    {
        return explode(" ", $commandText);
    }

    public function respond($text)
    {
        print "$text\n";
    }

    public function json($json)
    {
        print json_encode($json);
    }

}