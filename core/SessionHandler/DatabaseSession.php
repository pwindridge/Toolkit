<?php

namespace Toolkit\SessionHandler;

use \Toolkit\DataAccess\Database;


class DatabaseSession implements \SessionHandlerInterface {

    /**
     * @var Database
     */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->db->run_query(
            "CREATE TABLE IF NOT EXISTS `sessions` (" .
            " `sess_id` char(26) NOT NULL," .
            " `sess_data` text NOT NULL," .
            " `modified` int(11) NOT NULL," .
            " UNIQUE KEY `sess_id` (`sess_id`)" .
            ") ENGINE=InnoDB DEFAULT CHARSET=latin1; "
        );
        session_set_save_handler($this);
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $parameters = [
            'table' => 'sessions',
            'conditions' => [
                ['sess_id', '=', $session_id]
            ]
        ];
        $this->db->delete($parameters);
        return true;
    }

    public function gc($maxlifetime)
    {
        $parameters = [
            'table' => 'sessions',
            'conditions' => [
                ['modified', '<', time() - $maxlifetime]
            ]
        ];
        $this->db->delete($parameters);
        return true;
    }

    public function open($save_path, $name)
    {
        return true;
    }

    public function read($session_id)
    {
        $parameters = [
            'table' => 'sessions',
            'fields' => ['sess_data'],
            'conditions' => [['sess_id', '=', $session_id]]
        ];
        $result = $this->db->select($parameters);
        return empty($result) ? '' : $result[0]['sess_data'];
    }

    public function write($session_id, $session_data)
    {
        $parameters = [
            'table' => 'sessions',
            'fields' => ['sess_id', 'sess_data', 'modified'],
            'values' => [
                [$session_id, $session_data, time()]
            ]
        ];
        try {
            $this->db->insert($parameters);
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $parameters['set'] = [
                    'sess_data' => $session_data,
                    'modified' => time()
                ];
                $parameters['conditions'] = [
                    'conditions' => [['sess_id', '=', $session_id]]
                ];
                $this->db->update($parameters);
            }
        }
        return true;
    }
}