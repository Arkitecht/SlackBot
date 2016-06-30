<?php
namespace Arkitecht\SlackBot\Traits;

use PDO;

trait Queryable
{
    protected $db;

    public function connect($dsn, $username, $password)
    {
        $this->db = new PDO($dsn, $username, $password);

    }

    public function collection($query, $bindings = [], $format = PDO::FETCH_NUM)
    {
        $results = $this->fetchAll($query, $bindings, $format);
        if ($results) return collect($results);

        return collect();
    }

    public function lists($query, $bindings = [], $keys)
    {
        $args = func_get_args();

        $query = array_shift($args);
        $bindings = array_shift($args);
        $keys = $args;

        $collect = collect($this->fetchAll($query, $bindings, PDO::FETCH_ASSOC));

        return call_user_func_array([$collect, 'pluck'], $keys);
    }

    public function fetchAll($query, $bindings = [], $format = PDO::FETCH_NUM)
    {
        $prepare = $this->prepare($query, $bindings);
        if ($prepare) return $prepare->fetchAll($format);

        return null;
    }

    public function oneRow($query, $bindings = [])
    {
        $prepare = $this->prepare($query, $bindings);
        if ($prepare) return $prepare->fetch();

        return null;
    }

    public function oneColumn($query, $bindings = [], $column = 0)
    {
        $prepare = $this->prepare($query, $bindings);
        if ($prepare) return $prepare->fetchColumn($column);

        return null;
    }

    private function prepare($query, $bindings = [])
    {
        $prepare = $this->db->prepare($query);
        foreach ($bindings as $binding => $value)
            $prepare->bindValue(':' . $binding, $value);
        $execute = $prepare->execute();

        return $prepare;
    }
}