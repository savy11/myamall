<?php

namespace resources\models;

use Exception;
use resources\config\database;

final class model
{

    private $mysqli;
    private $connected = false;
    private $fn = null;

    public function __construct($fn, $main_db = false, $_host = null, $_user = null, $_pass = null, $_db = null)
    {
        $this->fn = $fn;
        if (database::connection) {
            mysqli_report(MYSQLI_REPORT_STRICT);
            if ($main_db) {
                $db_host = database::host;
                $db_user = database::user;
                $db_pass = database::pass;
                $db_db = database::db;
            } else if (!is_null($_host)) {
                $db_host = $_host;
                $db_user = $_user;
                $db_pass = $_pass;
                $db_db = $_db;
            } else {
                if ($fn->session('company') != '') {
                    $db_host = $fn->session('company', 'db_host');
                    $db_user = $fn->session('company', 'db_user');
                    $db_pass = $fn->session('company', 'db_pass');
                    $db_db = $fn->session('company', 'db_database');
                } else {
                    $db_host = database::host;
                    $db_user = database::user;
                    $db_pass = database::pass;
                    $db_db = database::db;
                }
            }
            try {
                $this->mysqli = new \mysqli($db_host, $db_user, $db_pass, $db_db);
                $this->connected = true;
            } catch (Exception $ex) {
                throw new Exception('<h1>Error establishing a database connection.</h1>');
            }
        }
    }

    public function __destruct()
    {
        if (database::connection) {
            if ($this->connected) {
                $this->mysqli->close();
            }
        }
    }

    public function trans_start()
    {
        $this->mysqli->autocommit(false);
    }

    public function trans_rollback()
    {
        $this->mysqli->rollback();
    }

    public function trans_commit()
    {
        $this->mysqli->commit();
    }

    /*
     * Query
     */

    public function query($sql)
    {
        $type = '';
        if (strpos(strtolower($sql), 'insert') !== false) {
            $type = 'insert';
        } else if (strpos(strtolower($sql), 'update') !== false) {
            $type = 'update';
        }
        if (!$this->mysqli->query($sql)) {
            throw new Exception($this->mysqli->error);
        }
        if ($type == 'insert') {
            return $this->mysqli->insert_id;
        }
    }

    /*
     * Insert
     */

    public function insert($table, $columns = array())
    {

        if (!$columns) {
            throw new Exception('Fields not found.');
        }
        $sql = "INSERT INTO `{$table}` SET ";
        foreach ($columns as $k => $v) {
            $v = $this->fn->replace_sql($v);
            if (strpos($k, 'password') !== false) {
                $v = $this->fn->encrypt($v);
            }
            $sql .= "`{$k}`='" . $v . "', ";
        }
        $sql = rtrim($sql, ', ');
        if (!$this->mysqli->query($sql)) {
            throw new Exception($this->mysqli->error);
        }
        return $this->mysqli->insert_id;
    }

    /*
     * Update
     */

    public function update($table, $columns = array(), $where = array())
    {
        if (!$columns) {
            throw new Exception('Fields not found.');
        }
        if (!$where) {
            throw new Exception('Where clause not found.');
        }
        $sql = "UPDATE `{$table}` SET ";
        foreach ($columns as $k => $v) {
            $v = $this->fn->replace_sql($v);
            if (strpos($k, 'password') !== false) {
                $v = $this->fn->encrypt($v);
            }
            $sql .= "`{$k}`='" . $v . "', ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= " WHERE ";
        foreach ($where as $k => $v) {
            $sql .= "`{$k}`='" . $this->fn->replace_sql($v) . "' AND ";
        }
        $sql = rtrim($sql, "AND ");
        if (!$this->mysqli->query($sql)) {
            throw new Exception($this->mysqli->error);
        }
    }

    /*
     * Batch
     */

    public function batch($type, $table, $columns = array(), $w = '')
    {
        if (!$columns) {
            throw new Exception('Fields not found.');
        }
        if ($type == 'update') {
            if (!$w) {
                throw new Exception('Where clause not found.');
            }
        }
        $sql = "";

        foreach ($columns as $key => $data) {
            $where = array();
            if ($this->fn->varv('' . $key . '', $w)) {
                $where = $w[$key];
            }
            $q = strtoupper($type) . ($type == 'insert' ? ' INTO' : '') . " `{$table}` SET ";
            foreach ($data as $k => $v) {
                $v = $this->fn->replace_sql($v);
                if (strpos($k, 'password') !== false) {
                    $v = $this->fn->encrypt($v);
                }
                $q .= "`{$k}`='" . $v . "', ";
            }
            $q = rtrim($q, ", ");
            if ($type == 'update') {
                $q .= " WHERE ";
                if (is_array($where)) {
                    foreach ($where as $k1 => $v1) {
                        $q .= "`{$k1}`='" . $this->fn->replace_sql($v1) . "' AND ";
                    }
                    $q = rtrim($q, "AND ");
                } else {
                    $q .= $where;
                }
            }
            $sql .= $q . ";";
        }
        if (!$this->mysqli->multi_query($sql)) {
            throw new Exception($this->mysqli->error);
        }
        do {
            if ($result = $this->mysqli->store_result()) {
                $result->free();
            }
            if (!$this->mysqli->more_results()) {
                break;
            }
        } while ($this->mysqli->next_result());
    }

    /*
     * Batch Query
     */

    public function batch_query($sql)
    {
        if (!$this->mysqli->multi_query($sql)) {
            throw new Exception($this->mysqli->error);
        }
        do {
            if ($result = $this->mysqli->store_result()) {
                $result->free();
            }
            if (!$this->mysqli->more_results()) {
                break;
            }
            if ($this->mysqli->errno) {
                throw new Exception($this->mysqli->error);
            }
        } while ($this->mysqli->next_result());
    }

    /*
     * Delete
     */

    public function delete($table, $where = '')
    {
        if (!$where) {
            throw new Exception('Where clause not found.');
        }
        $sql = "DELETE FROM `{$table}` WHERE ";
        if (is_array($where) && count($where) > 0) {
            foreach ($where as $k => $v) {
                $sql .= "`{$k}`='" . $this->fn->replace_sql($v) . "' AND ";
            }
            $sql = rtrim($sql, "AND ");
        } else {
            $sql .= $where;
        }
        if (!$this->mysqli->query($sql)) {
            throw new Exception($this->mysqli->error);
        }
    }

    /*
     * Select
     */

    public function select($sql)
    {
        $data = array();
        $res = $this->mysqli->query($sql);
        if (!$res) {
            throw new Exception($this->mysqli->error);
        }
        if ($obj = $res->fetch_assoc()) {
            $data = $obj;
        }
        $res->close();
        return $data;
    }

    /*
     * Select All
     */

    public function selectall($sql, $assoc = false)
    {
        $data = array();
        $res = $this->mysqli->query($sql);
        if (!$res) {
            throw new Exception($this->mysqli->error);
        }
        while ($obj = $res->fetch_assoc()) {
            if ($assoc) {
                $data[] = array_values($obj);
            } else {
                $data[] = $obj;
            }
        }
        $res->close();
        return $data;
    }

    /*
     * Count
     */

    public function count($sql)
    {
        $cnt = 0;
        $res = $this->mysqli->query($sql);
        if (!$res) {
            throw new Exception($this->mysqli->error);
        }
        $cnt = $res->num_rows;
        $res->close();
        return $cnt;
    }

    /*
     * Value Exists
     */

    public function value_exists($table, $column, $value, $id = '', $req_id = '')
    {
        if ($value != '') {
            $sql = "SELECT {$column} FROM {$table} WHERE {$column}='" . $value . "'" . (($id != '') ? " AND " . $id . "!='" . $req_id . "'" : '');
            if ($this->count($sql) > 0) {
                return true;
            }
        }
        return false;
    }

    /*
     * Get Value
     */

    public function get_value($table, $column, $where = '')
    {
        $sql = "SELECT {$column} FROM {$table} " . (($where != '') ? " WHERE " . $where : "");
        $res = $this->mysqli->query($sql);
        if (!$res) {
            throw new Exception($this->mysqli->error);
        }
        if ($row = $res->fetch_assoc()) {
            return $row[$column];
        }
        $res->close();
        return false;
    }

    /*
     * Freg Query
     */

    public function freg($query, $freg = array(), $value)
    {
        $data = array();
        $r = "\$data[\$obj['" . implode("']][\$obj['", $freg) . "']] = \$obj['" . $value . "'];";
        if ($result = $this->mysqli->query($query)) {
            while ($obj = $result->fetch_assoc()) {
                eval($r);
            }
            $result->close();
            return $data;
        } else {
            throw new Exception($this->mysqli->error);
        }
        return false;
    }

    /*
     * Freg All
     */

    function freg_all($query, $freg, $value = NULL, $assoc = TRUE, $array_name = NULL)
    {
        $data = array();
        if (strtoupper(gettype($freg)) == 'ARRAY') {
            if ($value) {
                $Str = array();
                foreach ($value as $v) {
                    $Str[] = "'" . $v . "' => \$obj['" . $v . "']";
                }
                if ($assoc) {
                    $r = "\$data[\$obj['" . implode("']][\$obj['", $freg) . "']] = array(" . implode(", ", $Str) . ");";
                } else {
                    $r = "\$data[\$obj['" . implode("']][\$obj['", $freg) . "']] = array_values(array(" . implode(", ", $Str) . "));";
                }
            } else {
                if ($assoc) {
                    $r = "\$data[\$obj['" . implode("']][\$obj['", $freg) . "']][] = \$obj;";
                } else {
                    $r = "\$data[\$obj['" . implode("']][\$obj['", $freg) . "']][] = array_values(\$obj);";
                }
            }
        } else {
            if ($array_name) {
                if ($assoc) {
                    $r = "\$v = \$obj['" . $freg . "']; unset(\$obj['" . $freg . "']); \$data[\$v]['" . $array_name . "'][] = \$obj;";
                } else {
                    $r = "\$v = \$obj['" . $freg . "']; unset(\$obj['" . $freg . "']); \$data[\$v]['" . $array_name . "'][] = array_values(\$obj);";
                }
            } else {
                if ($assoc) {
                    $r = "\$v = \$obj['" . $freg . "']; unset(\$obj['" . $freg . "']); \$data[\$v][] = \$obj;";
                } else {
                    $r = "\$v = \$obj['" . $freg . "']; unset(\$obj['" . $freg . "']); \$data[\$v][] = array_values(\$obj);";
                }
            }
        }

        if ($result = $this->mysqli->query($query)) {
            while ($obj = $result->fetch_assoc()) {
                eval($r);
            }
            $result->close();
            return $data;
        } else {
            throw new Exception($this->mysqli->error);
        }
        return false;
    }

    /*
     * Printer SQL
     */

    public function printer_sql($str)
    {
        $str = trim($str);
        $str = $this->mysqli->real_escape_string($str);
        return $str;
    }

    /*
     * File SQL
     */

    public function file_sql($str)
    {
        $str = $this->mysqli->real_escape_string($str);
        return $str;
    }

    /*
     * Get CSV String
     */

    public function get_csv_string($data)
    {
        $str = '';
        foreach ($data as $k => $v) {
            $data[$k] = str_replace('"', '', $v);
        }
        $str = '"' . implode('","', $data) . '"';
        return $str;
    }

    /*
     * CSV Query
     */

    public function csv_query($query)
    {
        $data = array();
        $flag = true;
        if ($result = $this->mysqli->query($query)) {
            while ($obj = $result->fetch_assoc()) {
                if ($flag) {
                    $data[] = '"' . implode('","', array_keys($obj)) . '"';
                    $flag = false;
                }
                $data[] = $this->get_csv_string($obj);
            }
            $result->close();
            return $data;
        } else {
            throw new Exception("MySQL Error: " . $this->mysqli->error);
        }
        return false;
    }

}
