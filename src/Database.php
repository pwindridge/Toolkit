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
    		$fields = array();
    		$types = array();
    		$values = array();

    		foreach ($parameters['conditions'] as $field=>$value) {
    			if(is_numeric($value)) {
    				$types[] = \PDO::PARAM_INT;
				} else {
    				$types[] = \PDO::PARAM_STR;
				}
				$values[] = $value;
				$fields[] = '`' . $field . '` = ?';
			}
    		$sql .= ' WHERE ' . implode(' AND ', $fields);

    		$sth = $this->dbh->prepare($sql);

    		for ($i = 0; $i < count($parameters['conditions']); $i++) {
    			$sth->bindValue(1 + $i, $values[$i], $types[$i]);
			}

			$sth->execute();
		} else {
			$sth = $this->dbh->query($sql);
		}

		return $sth->fetchAll(\PDO::FETCH_ASSOC);
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