<?php
/**
 * Created by PhpStorm.
 * User: pcw1
 * Date: 01/11/2017
 * Time: 13:06
 */
use \Toolkit\Database;

class DatabaseTest extends PHPUnit_Framework_TestCase {
	private $db;
	private $dbh;

	protected function setUp() {
		$cfg['db']['host'] = 'localhost';
		$cfg['db']['db'] = 'test';
		$cfg['db']['user'] = 'test';
		$cfg['db']['pass'] = 'test';


		$this->dbh = new PDO(
			'mysql:host=' . $cfg['db']['host'] .
			';dbname='. $cfg['db']['db'],
			$cfg['db']['user'],
			$cfg['db']['pass']
		);

		$sqlTable = <<<CREATETABLE
            CREATE TABLE IF NOT EXISTS `testtable` (
              `Id` int(4) NOT NULL AUTO_INCREMENT,
              `FirstName` varchar(50) NOT NULL,
              `Surname` varchar(50) NOT NULL,
              PRIMARY KEY (`Id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
CREATETABLE;

		$sqlInsert = <<<CREATEQUERY
            INSERT INTO `testtable` (`Id`, `FirstName`, `Surname`) VALUES
            (1, 'Philip', 'Windridge'),
            (2, 'Robin', 'Oldham');
CREATEQUERY;

		$this->dbh->query($sqlTable);
		$this->dbh->query($sqlInsert);

		$this->db = new Database($cfg);
	}

	public function tearDown() {
		$this->dbh->query('DROP TABLE `testtable`');
	}

	/**
	 * @test
	 */
	public function SimpleSelect() {
		$parameters = array(
			'fields'=>array('*'),
			'table'=>'testtable'
		);
		$expected = array(
			array('Id'=>1, 'FirstName'=>'Philip', 'Surname'=>'Windridge'),
			array('Id'=>2, 'FirstName'=>'Robin', 'Surname'=>'Oldham')
		);
		$this->assertEquals($expected, $this->db->select($parameters));
	}

	/**
	 * @test
	 */
	public function SelectWithTwoFields() {
		$parameters = array(
			'fields'=>array('FirstName', 'Surname'),
			'table'=>'testtable'
		);
		$expected = array(
			array('FirstName'=>'Philip', 'Surname'=>'Windridge'),
			array('FirstName'=>'Robin', 'Surname'=>'Oldham')
		);
		$this->assertEquals($expected, $this->db->select($parameters));
	}

	/**
	 * @test
	 */
	public function SelectWithSimpleCondition() {
		$parameters = array(
			'fields'=>array('FirstName', 'Surname'),
			'table'=>'testtable',
			'conditions'=>array('FirstName'=>'Robin')
		);
		$expected = array(
			array('FirstName'=>'Robin', 'Surname'=>'Oldham')
		);
		$this->assertEquals($expected, $this->db->select($parameters));
	}

	/**
	 * @test
	 */
	public function SelectWithTwoConditions() {
		$parameters = array(
			'fields'=>array('FirstName', 'Surname'),
			'table'=>'testtable',
			'conditions'=>array('FirstName'=>'Philip', 'Surname'=>'Windridge')
		);
		$expected = array(
			array('FirstName'=>'Philip', 'Surname'=>'Windridge')
		);
		$this->assertEquals($expected, $this->db->select($parameters));
	}
}
