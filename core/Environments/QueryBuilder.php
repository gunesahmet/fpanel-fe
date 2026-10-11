<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace FPanel\Environments;

use PDOException;
use PDOStatement;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Db;
use Phalcon\Mvc\User\Component;

class QueryBuilder extends Component
{
    protected $sql              = null;
    protected $columns          = array();
    protected $qwhere           = array();
    protected $and_where        = array();
    protected $or_where         = array();
    protected $order_columns    = array();
    protected $inner_join       = array();
    protected $left_join        = array();
    protected $edit_column      = array();
    protected $where            = null;
    protected $order            = null;
    protected $order_desc       = null;
    protected $order_asc        = null;
    protected $table            = null;
    protected $sqlResult        = null;
    protected $result           = null;
    protected $limit            = null;
    protected $distinct         = false;
    protected $select           = false;
    protected $insert           = false;
    protected $all              = false;
    protected $error_messages   = array();
    protected $assign           = null;
    protected $values           = null;
    protected $conditions       = array();
    protected $bind             = array();
    protected $group_by         = null;
    protected $debug_status     = true;
    /**
     * @var Ini
     */
    protected $config;

    /**
     * @var string
     */
    private $generated_sql = null;

    /**
     * init
     */
    public function __construct()
    {
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function debug($status): QueryBuilder
    {
        $this->debug_status = $status;
        return $this;
    }

    /**
     * @param array $columns
     * @param bool $distinct
     * @return $this
     */
    public function select(array $columns = array(), bool $distinct = false): QueryBuilder
    {
        $this->select = true;
        $column_list = null;
        foreach($columns as $col) {
            if($column_list != null)
                $column_list = $column_list . ',' . $col;
            else
                $column_list = $col;
        }

        foreach($this->explode(preg_replace('/\s+/', ' ', trim($column_list))) as $val) {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->columns[] =  $column;
            $this->columns[$column] =  $val;
        }

        if($distinct == true)
            $this->distinct = true;

        return $this;

    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function where(array $conditions = []): QueryBuilder
    {
        $condition = $conditions['conditions'];
        foreach($conditions['bind'] as $i => $bind) {
            $bind = strtolower($bind);
            $condition = str_replace("?$i","'$bind'", $condition);
        }
        $this->qwhere  = $condition;

        return $this;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function andWhere(array $conditions = []): QueryBuilder
    {
        $condition = $conditions['conditions'];
        foreach($conditions['bind'] as $i => $bind) {
            $bind = strtolower($bind);
            $condition = str_replace("?$i","'$bind'", $condition);
        }
        $this->and_where[]  = $condition;
        return $this;
    }

    /**
     * @param array $conditions
     * @return $this
     */
    public function orWhere(array $conditions = []): QueryBuilder
    {
        $condition = $conditions['conditions'];
        foreach($conditions['bind'] as $i => $bind) {
            $bind = strtolower($bind);
            $condition = str_replace("?$i","'$bind'", $condition);
        }
        $this->or_where[]  = $condition;
        return $this;
    }

    /**
     * @param string $order
     * @return $this
     */
    public function orderDesc(string $order): QueryBuilder
    {
        $this->order_desc = $order;
        return $this;
    }

    /**
     * @param string $order
     * @return $this
     */
    public function orderAsc(string $order): QueryBuilder
    {
        $this->order_asc = $order;
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit): QueryBuilder
    {
        $this->limit = " LIMIT $limit";
        return $this;
    }

    /**
     * @param string $offset
     * @return $this
     */
    public function limitOffSet(string $offset): QueryBuilder
    {
        $this->limit = $this->limit . " OFFSET $offset";
        return $this;
    }

    /**
     * @param string $table
     * @param string $relation
     * @return $this
     */
    public function innerJoin(string $table, string $relation): QueryBuilder
    {
        $this->inner_join[] = array('table' => $table,'relation' =>$relation);
        return $this;
    }

    /**
     * @param string $table
     * @param string $relation
     * @return $this
     */
    public function leftJoin(string $table, string $relation): QueryBuilder
    {
        $this->left_join[] = array('table' => $table,'relation' =>$relation);
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function groupBy(array $columns = []): QueryBuilder
    {
        $this->group_by = "";
        if(!empty($columns)) {
            foreach($columns as $column) {
                if($this->group_by == null)
                    $this->group_by = $column;
                else
                    $this->group_by .= "," . $column;
            }
        }
        $this->group_by = "GROUP BY $this->group_by";
        return $this;
    }

    /**
     * @return array
     */
    public function find(): array
    {
        $this->all = true;
        $this->generate();

        $this->debug->firebug($this->sql);

        $result_sql = $this->db->fetchAll($this->sql,Db::FETCH_ASSOC);

        $this->result = $result_sql;
        $this->clear();

        if(!empty($this->result)) {
            return $this->result;
        }
        else
            return [];
    }

    /**
     * @return array|bool
     */
    public function findFirst()
    {
        $this->all = false;
        $this->generate();

        $result_sql = $this->db->fetchOne($this->sql, Db::FETCH_ASSOC);

        $this->clear();
        if(($result_sql != false) && (!empty($result_sql)))
            return $result_sql;
        else
            return false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this->result;
    }

    public function toSql(): ?string
    {
        return $this->generated_sql;
    }
    /**
     *
     */
    private function generate()
    {
        $select_columns = "";
        foreach($this->columns as $key => $value) {
            if (!is_numeric($key)) {
                if($select_columns == null)
                    $select_columns = $value;
                else
                    $select_columns = $select_columns . ',' . $value;
            }
        }

        $left_join = "";
        if(!empty($this->left_join)) {
            foreach ($this->left_join as $value) {
                if($left_join == null)
                    $left_join = ' LEFT JOIN '. $value['table'] .' ON '. $value['relation'];
                else
                    $left_join = $left_join . ' LEFT JOIN '. $value['table'] .' ON '. $value['relation'];
            }
        }

        $inner_join = "";
        if(!empty($this->inner_join)) {
            foreach ($this->inner_join as $value) {
                if($inner_join == null)
                    $inner_join = ' INNER JOIN '. $value['table'] .' ON '. $value['relation'];
                else
                    $inner_join = $inner_join . ' INNER JOIN '. $value['table'] .' ON '. $value['relation'];
            }
        }

        $qwhere = "";
        $and_where = "";
        $or_where = "";

        if(!empty($this->qwhere)) {
            $qwhere = ' WHERE '. $this->qwhere;

            if(!empty($this->and_where)) {
                foreach($this->and_where as $value) {
                    if($and_where == null)
                        $and_where = ' AND '. $value;
                    else
                        $and_where = $and_where . ' AND '. $value;
                }
            }

            if(!empty($this->or_where)) {
                foreach($this->or_where as $value) {
                    if($or_where == null)
                        $or_where = ' OR '. $value;
                    else
                        $or_where = $or_where . ' OR '. $value;
                }
            }
        }

        $where = $qwhere . $and_where. $or_where;
        $this->where = $where;

        $order = null;
        if($this->order_desc != null) {
            $order = " ORDER BY $this->order_desc DESC";
        }

        if($this->order_asc != null) {
            $order = " ORDER BY $this->order_asc ASC";
        }

        $select = "SELECT ";
        if($this->distinct == true) {
            if((strpos($this->system_packages["DATABASE_SERVERS"], "mysql") === false) && strpos($this->system_packages["DATABASE_SERVERS"], "maria") === false) {
                if((isset($this->order_asc)) || (isset($this->order_desc))) {
                    $select = $select . " DISTINCT ";
                    if(isset($this->order_desc))
                        $select = $select . "ON($this->order_desc) ";
                    if(isset($this->order_asc))
                        $select = $select . "ON($this->order_asc) ";
                }
            }
            else {
                $select = $select . " DISTINCT ";
            }
        }

        $this->group_by = " ".$this->group_by;
        $this->sql = $select . $select_columns.' FROM '. $this->table . $inner_join . $left_join . $this->where . $this->group_by . $order .  $this->limit;
        $this->generated_sql = $this->sql;
    }

    private function explode($str): array
    {
        $retval = array();
        $hold = array();
        $balance = 0;
        $parts = explode(',', $str);

        foreach($parts as $part) {
            $hold[] = $part;
            $balance += $this->balanceChars($part);

            if(($balance > 0) && ($balance < 2)) {
                $retval[] = implode(',', $hold);
                $hold = array();
                $balance = 0;
            }
        }

        if(count($hold) > 0)
            $retval[] = implode(',', $hold);

        return $retval;
    }

    /**
     * @param $str
     * @return int
     */
    private function balanceChars($str): int
    {
        $openCount = substr_count($str, '(');
        $closeCount = substr_count($str, ')');
        return $openCount - $closeCount;
    }

    /**
     *
     */
    private function clear()
    {

        $this->sql              = array();
        $this->columns          = array();
        $this->qwhere           = array();
        $this->and_where        = array();
        $this->or_where         = array();
        $this->order_columns    = array();
        $this->inner_join       = array();
        $this->left_join        = array();
        $this->edit_column      = array();
        $this->where            = null;
        $this->order            = null;
        $this->order_desc       = null;
        $this->order_asc        = null;
        $this->table            = null;
        $this->sqlResult        = null;
        $this->limit            = null;
        $this->distinct         = false;
        $this->select           = false;
        $this->insert           = false;
        $this->all              = false;
        $this->error_messages   = array();
        $this->conditions       = array();
        $this->bind             = array();
        $this->assign           = null;
        $this->values           = null;
        $this->group_by         = null;

    }

    /**
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns): QueryBuilder
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function into($table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $assign
     * @return $this
     */
    public function assign(array $assign): QueryBuilder
    {
        $this->assign[] = $assign;
        return $this;
    }

    /**
     *
     */
    public function create(): bool
    {
        $columns = null;
        foreach($this->columns as $column) {
            if($columns == null)
                $columns = "`$column`";
            else
                $columns .= ",`$column`";
        }
        $status = $this->insert($this->table, $columns, $this->assign);
        $this->clear();
        return $status;
    }

    /**
     * @param $table
     * @param $columns
     * @param $assigns
     * @return bool
     */
    private function insert($table, $columns, $assigns): bool
    {
        if(empty($assigns)) {
            return true;
        }
        $max_insert = 1000;
        $values = null;
        $i = 0;
        foreach($assigns as $index => $array_values) {
            $str1 = "";
            foreach ($array_values as $value) {
                $value = str_replace("'","\'", $value);
                if($str1 == null)
                    $str1 = "'$value'";
                else
                    $str1 .= ",'$value'";
            }
            if($values == null)
                $values = "($str1)";
            else
                $values .= ",($str1)";
            $i++;
            if($i == $max_insert) {
                break;
            }
            unset($assigns[$index]);
        }

        $query = "INSERT IGNORE INTO $table ($columns) VALUES $values;";

        try {
            /**
             * @var $statement PDOStatement
             */
            $statement = $this->db->prepare($query);
            $statement->execute();
            if($statement->errorCode() != 0) {
                $this->error_messages[] = $statement->errorInfo();
                return false;
            }
        }
        catch(PDOException $e) {
            $this->error_messages["message"] = $e->getMessage();
            $this->error_messages["code"] = $e->getCode();
            $this->error_messages["trace"] = $e->getTrace();
            $this->log->error([
                "Module" => "QueryBuilder->insertInto",
                "data" => $e->getMessage(),
                "Query" => $query,
            ]);
        }

        $this->insert($table, $columns, $assigns);
        return true;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->error_messages;
    }
}