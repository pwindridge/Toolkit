<?php

use Toolkit\DataAccess\Database;



require __DIR__ . '/../vendor/autoload.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {

    private $db;

    public function setUp()
    {
        try {
            $pdo = new PDO(
                "mysql:host=127.0.0.1;dbname=test",
                'test',
                'test',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $pe) {
            die($pe->getMessage());
        }

        $sth_drop = $pdo->prepare("DROP TABLE IF EXISTS `test_table`");

        $sth_drop->execute();

        $sth_create = $pdo->prepare(
            "CREATE TABLE `test_table` (" .
            " `field1` char(20) NOT NULL," .
            " `field2` char(20)," .
            " `field3` int(11)," .
            " UNIQUE KEY `username` (`field1`)" .
            ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"
        );

        $sth_create->execute();

        $sth_lock = $pdo->prepare("LOCK TABLES `test_table` WRITE;");

        $sth_lock->execute();

        $sth_insert = $pdo->prepare(
            "INSERT INTO `test_table` (`field1`, `field2`, `field3`)" .
            " VALUES " .
            "('value1','value2', 1), ('value3','value4', 2), ('value5','value6', 3);"
        );

        $sth_insert->execute();

        $sth_unlock = $pdo->prepare("UNLOCK TABLES;");

        $sth_unlock->execute();

        $this->db = new Database(
            [
                'host' => '127.0.0.1',
                'dbname' => 'test',
                'user' => 'test',
                'password' => 'test',
                'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            ]
        );
    }

    public function test_select_all()
    {
        $parameters = [
            'fields' => ['*'],
            'table' => 'test_table'
        ];

        $expected = [
            ['field1' => 'value1', 'field2' => 'value2', 'field3' => 1],
            ['field1' => 'value3', 'field2' => 'value4', 'field3' => 2],
            ['field1' => 'value5', 'field2' => 'value6', 'field3' => 3]
        ];

        $this->assertEquals($expected, $this->db->select($parameters));
    }

    public function test_select_all_with_conditions()
    {
        $parameters = [
            'fields' => ['*'],
            'table' => 'test_table',
            'conditions' => [['field1', '=', 'value1']]
        ];

        $expected = [['field1' => 'value1', 'field2' => 'value2', 'field3' => 1]];

        $this->assertEquals($expected, $this->db->select($parameters));
    }

    public function test_select_all_with_conditions_numeric()
    {
        $parameters = [
            'fields' => ['*'],
            'table' => 'test_table',
            'conditions' => [['field3', '=', 3]]
        ];

        $expected = [['field1' => 'value5', 'field2' => 'value6', 'field3' => 3]];

        $this->assertEquals($expected, $this->db->select($parameters));
    }

    public function test_select_specific_field_with_conditions()
    {
        $parameters = [
            'fields' => ['field1'],
            'table' => 'test_table',
            'conditions' => [['field1', '=', 'value1']]
        ];

        $expected = [['field1' => 'value1']];

        $this->assertEquals($expected, $this->db->select($parameters));
    }

    public function test_select_multiple_fields_with_multiple_and_conditions()
    {
        $parameters = [
            'fields' => ['field1', 'field2', 'field3'],
            'table' => 'test_table',
            'conditions' => [
                ['field1', '=', 'value1'],
                ['field2', 'LIKE', '%lue2']
            ],
            'logic_operator' => 'OR'
        ];

        $expected = [['field1' => 'value1', 'field2' => 'value2', 'field3' => 1]];

        $this->assertEquals($expected, $this->db->select($parameters));
    }

    public function test_insert_single_record()
    {
        $parameters = [
            'table' => 'test_table',
            'fields' => ['field1', 'field2', 'field3'],
            'values' => [
                ['value7', 'value8', 4]
            ]
        ];

        $this->assertEquals(1, $this->db->insert($parameters));
    }

    public function test_insert_multiple_records()
    {
        $parameters = [
            'table' => 'test_table',
            'fields' => ['field1', 'field2', 'field3'],
            'values' => [
                ['value7', 'value8', 4],
                ['value9', 'value10', 5]
            ]
        ];

        $this->assertEquals(2, $this->db->insert($parameters));
    }

    public function test_update_one_field_with_no_conditions()
    {
        $parameters = [
            'set' => ['field2' => 'value1'],
            'table' => 'test_table'
        ];

        $this->assertEquals(3, $this->db->update($parameters));
    }

    public function test_update_two_field_with_no_conditions()
    {
        $parameters = [
            'set' => ['field2' => 'value1', 'field3' => 6],
            'table' => 'test_table'
        ];

        $this->assertEquals(3, $this->db->update($parameters));
    }

    public function test_update_two_field_with_conditions()
    {
        $parameters = [
            'set' => ['field2' => 'value1', 'field3' => 6],
            'table' => 'test_table',
            'conditions' => [
                ['field3', '=', 3]
            ]
        ];

        $this->assertEquals(1, $this->db->update($parameters));
    }

    public function test_update_two_field_with_greater_than_conditions()
    {
        $parameters = [
            'set' => ['field2' => 'value1', 'field3' => 6],
            'table' => 'test_table',
            'conditions' => [
                ['field3', '>', 1]
            ]
        ];

        $this->assertEquals(2, $this->db->update($parameters));
    }

    public function test_delete_all_records()
    {
        $parameters = [
            'table' => 'test_table'
        ];

        $this->assertEquals(3, $this->db->delete($parameters));
    }

    public function test_delete_one_record_where()
    {
        $parameters = [
            'table' => 'test_table',
            'conditions' => [
                ['field3', '=', 1]
            ]
        ];

        $this->assertEquals(1, $this->db->delete($parameters));
    }

    public function test_delete_two_records_where_or()
    {
        $parameters = [
            'table' => 'test_table',
            'conditions' => [
                ['field3', '=', 1],
                ['field2', '=', 'value4']
            ],
            'logic_operator' => 'OR'
        ];

        $this->assertEquals(2, $this->db->delete($parameters));
    }
}
