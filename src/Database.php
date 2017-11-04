<?php

namespace Toolkit;

class Database
{
    private $dbh;

    public function __construct($cfg)
    {
        $this->dbh = new \PDO(
            'mysql:host=' . $cfg['db']['host'] .
            ';dbname=' . $cfg['db']['db'],
            $cfg['db']['user'],
            $cfg['db']['pass']);
        ;
    }

    public function select($parameters)
    {
    	$fields = array('*');

    	if ($parameters['fields']) {
			if($parameters['fields'][0] != '*') {
				$fields = array();
				foreach($parameters['fields'] as $field) {
					$fields[] = '`' . $field . '`';
				}
			}
		}
    	$sql = 'SELECT ' . implode(', ', $fields) .
			' FROM `' . $parameters['table'] . '`';

    	if (isset($parameters['conditions'])) {
    		$sth = $this->prepareWithWhereConditions(
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

    private function prepareWithWhereConditions($conditions, $sql) {
		$fields = array();
		$types = array();
		$values = array();

		if (
			count($conditions['fieldValue']) <>
			count($conditions['valueType'])) {
			throw new \Exception(
				'Number of value types and values don\'t match.'
			);
		}

		if (isset($conditions['valueType'])) {
			foreach ($conditions['valueType'] as $type) {
				if ($type == 'string') {
					$types[] = \PDO::PARAM_STR;
				} else {
					$types[] = \PDO::PARAM_INT;
				}
			}
		} else {
			throw new \Exception(
				'Missing value types for conditions.'
			);
		}
		$count = 0;
		foreach ($conditions['fieldValue'] as $field=>$value) {
			$values[] = $value;
			$fields[] = '`' . $field . '` ' .
				$conditions['comparison'][$count++] .
				' ?';
		}
		$sql .= ' WHERE ' . implode(' AND ', $fields);

		$sth = $this->dbh->prepare($sql);

		for ($i = 0; $i < count($values); $i++) {
			$sth->bindValue(1 + $i, $values[$i], $types[$i]);
		}

		$sth->execute();

		return $sth;
	}

    public function insert($parameters)
    {

    }

    public function update($parameters)
    {

    }

    public function delete($parameters)
    {

    }
}