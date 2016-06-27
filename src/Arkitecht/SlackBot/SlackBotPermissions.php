<?php
namespace Arkitecht\SlackBot;


class SlackBotPermissions
{
    private $target;
    private $allowed_tokens;
    private $allowed_teams;
    private $allowed_users;
    private $errorMesage = 'You are not permitted to access this SlackBot';

    public function __construct($allowed_tokens = [], $allowed_teams = [], $allowed_users = [])
    {
        $this->allowed_tokens = collect($allowed_tokens);
        $this->allowed_teams = collect($allowed_teams);
        $this->allowed_users = collect($allowed_users);
    }

    public function setErrorMessage($message)
    {
        $this->errorMesage = $message;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function checkAccess()
    {
        if ($this->allowed_tokens->count()) {
            if (!$this->findInCollection($this->allowed_tokens, $this->target->getRequestInput('token')))
                return $this->throwError();
        }
        if ($this->allowed_teams->count()) {
            if (!$this->findInCollection($this->allowed_teams, $this->target->getRequestInput('team_id')))
                return $this->throwError();
        }
        if ($this->allowed_users->count()) {
            if (!$this->findInCollection($this->allowed_users, $this->target->getRequestInput('user_name')))
                return $this->throwError();
        }

        return true;
    }
    
    public function findInCollection($collection, $search)
    {
        return $collection->first(function ($key, $value) use ($search) {
            return $value == $search;
        });
    }

    public function throwError()
    {
        $this->target->respond($this->errorMesage);

        return false;
    }

}