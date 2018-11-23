<?php

namespace Toolkit\DataAccess;


class Database {

    private $pdo;

    public function __construct(array $config)
    {
        $this->pdo = new \PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']}",
            $config['user'],
            $config['password'],
            $config['options']
        );
    }

    public function select(array $parameters)
    {
        $sql = "SELECT " . $this->get_fields($parameters['fields']) . " FROM `{$parameters['table']}`";

        $sth = isset($parameters['conditions']) ?
            $this->prepare_where($parameters, $sql) :
            $this->pdo->prepare("{$sql};");

        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert(array $parameters)
    {
        $sth = $this->pdo->prepare(
            "INSERT INTO `{$parameters['table']}`".
            " (" .
            $this->get_fields($parameters['fields']) .
            ")" .
            " VALUES " .
            $this->prepare_placeholders(count($parameters['fields']), count($parameters['values'])) .
            ";"
        );

        $count = 1;
        foreach ($parameters['values'] as $record) {
            for ($i = 0; $i < count($parameters['fields']); $i++) {
                $sth->bindParam($count++, $record[$i]);
            }
        }

        $sth->execute();

        return $sth->rowCount();
    }

    private function get_fields(array $raw_fields)
    {
        foreach ($raw_fields as $field) {
            $select_fields[] = $field == '*' ? $field : "`{$field}`";
        }
        return join(', ', $select_fields);
    }

    private function prepare_placeholders(int $value_count, int $record_count)
    {
        for ($i = 0; $i < $record_count; $i++) {
            for ($j = 0; $j < $value_count; $j++) {
                $record[] = "?";
            }
            $formatted_records[] = '(' . join(', ', $record) . ')';
            $record = [];
        }

        return join(', ', $formatted_records);
    }

    public function update(array $parameters)
    {
        $sql = "UPDATE `{$parameters['table']}` SET " . $this->get_set_values($parameters['set']);

        $sth = isset($parameters['conditions']) ?
            $this->prepare_where($parameters, $sql) :
            $this->pdo->prepare("{$sql};");

        $sth->execute();

        return $sth->rowCount();
    }

    private function get_set_values(array $field_values)
    {
        foreach ($field_values as $field => $value) {
            $set_values[] = "`{$field}` = " . $this->punctuate($value);
        }
        return join(', ', $set_values);
    }

    public function delete(array $parameters)
    {
        $sql = "DELETE FROM `{$parameters['table']}`";

        $sth = isset($parameters['conditions']) ?
            $this->prepare_where($parameters, $sql) :
            $this->pdo->prepare("{$sql};");

        $sth->execute();

        return $sth->rowCount();
    }

    private function prepare_where(array $parameters, string $sql)
    {
        $conditions = [];
        foreach ($parameters['conditions'] as $condition) {
            $conditions[] = "`{$condition[0]}` {$condition[1]} :{$condition[0]}";
        }

        $glue = $parameters['logic_operator'] ?? 'AND';

        $sth = $this->pdo->prepare("{$sql} WHERE " . join(" {$glue} ", $conditions) . ";");

        foreach ($parameters['conditions'] as $condition) {
            $sth->bindParam(":{$condition[0]}", $condition[2]);
        }
        return $sth;
    }

    private function punctuate($value)
    {
        return is_numeric($value) ? $value : "'$value'";
    }

    public function run_query(string $sql)
    {
        $sth = $this->pdo->prepare("{$sql};");
        $sth->execute();
    }

}