<?php

namespace Toolkit;

class Database {

    private $dbh;

    public function __construct($cfg)
    {
        $this->dbh = new \PDO(
            'mysql:host=' . $cfg['db']['host'] .
            ';dbname=' . $cfg['db']['db'],
            $cfg['db']['user'],
            $cfg['db']['pass']);;
    }

    public function select($parameters)
    {
        $fields = array ('*');

        if (isset($parameters['fields']) && $parameters['fields'][0] != '*') {
            $fields = $this->prepareFields($parameters['fields']);
        }
        $sql = 'SELECT ' . implode(', ', $fields) .
            ' FROM `' . $parameters['table'] . '`';

        if (isset($parameters['conditions']['fieldValue'])) {
            $sth = $this->addWhere(
                $parameters['conditions'],
                $sql
            );
        } else {
            $sth = $this->dbh->query($sql);
        }

        if ($sth) {
            return $sth->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function insert($parameters)
    {
        $recordsPlaceholders = array ();
        $values = array();
        foreach ($parameters['records'] as $record) {
            $placeholders = array();
            for ($i = 0; $i < count($record); $i++) {
                $placeholders[] = '?';
                $values[] = $record[$i];
            }
            $recordsPlaceholders[] = implode(', ', $placeholders);
        }

        $sql = 'INSERT INTO ' . $parameters['table'] .
            ' (' . implode(', ', $this->prepareFields($parameters['fields'])) . ')' .
            ' VALUES (' . implode('), (', $recordsPlaceholders) . ')'
        ;
        $sth = $this->dbh->prepare($sql);
        $this->bindAndExecute($sth, $values);

        return $sth->rowCount();
    }

    public function update($parameters)
    {

    }

    public function delete($parameters)
    {

    }

    private function addWhere($conditions, $sql)
    {
        $fields = array ();
        $values = array ();

        foreach ($conditions['fieldValue'] as $fieldValue) {
            $values[] = current($fieldValue);
            $fields[] = '`' . key($fieldValue) . '` ' .
                $fieldValue['join'] .
                ' ?';
        }
        $sql .= ' WHERE ' . implode(' AND ', $fields);

        $sth = $this->dbh->prepare($sql);
        $this->bindAndExecute($sth, $values);

        return $sth;
    }

    private function prepareFields($fields)
    {
        $preparedFields = array ();
        foreach ($fields as $field) {
            $preparedFields[] = '`' . $field . '`';
        }
        return $preparedFields;
    }

    private function bindAndExecute(\PDOStatement $sth, $values) {
        for ($i = 0; $i < count($values); $i++) {
            $sth->bindValue($i + 1, $values[$i]);
        }
        $sth->execute();
    }
}