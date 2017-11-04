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
    		$conditions = array();
    		$values = array();
    		$count = 0;
    		foreach ($parameters['conditions'] as $field=>$value) {
    			if(!is_numeric($value)) {
    				$value = '\'' . $value . '\'';
				}
				$values[] = $value;
				$conditions[] = '`' . $field . '`=:value' . $count++;
			}
    		$sql .= ' WHERE ' . implode(' AND ', $conditions) . ';';

    		$sth = $this->dbh->prepare($sql);

    		for ($i = 0; $i < count($parameters['conditions']); $i++) {
    			$sth->bindParam(':value' . $i, $values[$i]);
			}
			$sth->execute();
		} else {
			$sth = $this->dbh->prepare($sql);
			$sth->execute();
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