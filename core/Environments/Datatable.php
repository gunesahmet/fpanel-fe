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

use FPanel\Library\Library;
use Phalcon\Db;

/**
 * Class Datatable
 * @package FPanel\Datatable
 */
class Datatable extends Library
{
    protected $sql              = null;
    protected $total_record_sql = null;
    protected $select           = array();
    protected $columns          = array();
    protected $qwhere           = array();
    protected $and_where        = array();
    protected $or_where         = array();
    protected $order_columns    = array();
    protected $limit            = array();
    protected $unset_column     = array();
    protected $add_column       = array();
    protected $edit_column      = array();
    protected $search_column    = array();
    protected $inner_join       = array();
    protected $left_join        = array();
    protected $distinct = false;
    protected $where = null;
    protected $order = null;
    protected $order_desc = null;
    protected $order_asc = null;
    protected $table = null;
    protected $aaData = null;
    protected $iCnt = null;

    public function initialize()
    {
        $this->db = $this->getDI()->get('db');
    }

    public function select(array $columns = array(), bool $distinct = false): Datatable
    {
        foreach($columns as $col) {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $col));
            $this->columns[] =  $column;
            $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $col));
        }
        if($distinct)
            $this->distinct = true;
        return $this;
    }

    public function unsetColumns(string $unset_columns): Datatable
    {
        $this->unset_column[] = $unset_columns;
        return $this;
    }

    public function addColumn($column, $content, $match_replacement = NULL): Datatable
    {
        $this->add_column[$column] = array('content' => $content, 'replacement' => $this->explode($match_replacement));
        return $this;
    }

    public function editColumn($column, $content, $match_replacement): Datatable
    {
        $this->edit_column[$column][] = array('content' => $content, 'replacement' => $this->explode($match_replacement));
        return $this;
    }

    public function from($table): Datatable
    {
        $this->table = $table;
        return $this;
    }

    public function where($conditions): Datatable
    {
        $this->qwhere[]  = $conditions;
        return $this;
    }

    public function andWhere($conditions): Datatable
    {
        $this->and_where[]  = $conditions;
        return $this;
    }

    public function orWhere($conditions): Datatable
    {
        $this->or_where[]  = $conditions;
        return $this;
    }

    public function orderDesc($order): Datatable
    {
        $this->order_desc = $order;
        return $this;
    }

    public function orderAsc($order): Datatable
    {
        $this->order_asc = $order;
        return $this;
    }

    public function limit($limit): Datatable
    {
        $this->limit[] = $limit;
        return $this;
    }

    public function innerJoin($table, $relation): Datatable
    {
        $this->inner_join[] = array('table' => $table,'relation' =>$relation);
        return $this;
    }

    public function leftJoin($table, $relation): Datatable
    {
        $this->left_join[] = array('table' => $table,'relation' =>$relation);
        return $this;
    }

    public function searchColumn(array $columns = array()): Datatable
    {
        foreach($columns as $col)
            $this->search_column[] = $col;
        return $this;
    }

    public function generate(): Datatable
    {
        $select_columns = "";
        foreach($this->select as $key => $value) {
            if (!is_numeric($key)) {
                if($select_columns == null)
                    $select_columns = $value;
                else
                    $select_columns = $select_columns . ',' . $value;
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

        $left_join = "";
        if(!empty($this->left_join)) {
            foreach ($this->left_join as $value) {
                if($left_join == null)
                    $left_join = ' LEFT JOIN '. $value['table'] .' ON '. $value['relation'];
                else
                    $left_join = $left_join . ' LEFT JOIN '. $value['table'] .' ON '. $value['relation'];
            }
        }

        $qwhere = "";
        if(!empty($this->qwhere)) {
            foreach($this->qwhere as $value) {
                if($qwhere == null)
                    $qwhere = $value;
            }
        }

        $and_where = "";
        if(!empty($this->and_where)) {
            foreach($this->and_where as $value) {
                if($and_where == null)
                    $and_where = ' AND '. $value;
                else
                    $and_where = $and_where . ' AND '. $value;
            }
        }

        $or_where = "";
        if(!empty($this->or_where)) {
            foreach($this->or_where as $value) {
                if($or_where == null)
                    $or_where = ' OR '. $value;
                else
                    $or_where = $or_where . ' OR '. $value;
            }
        }

        $where = $qwhere . $and_where. $or_where;
        $this->where = $where;
        $this->getFiltering();
        $this->getOrdering();
        $order = $this->order;

        $select = "SELECT ";

        $sLimit = '';
        $iDisplayStart = $this->request->getPost('iDisplayStart');
        $iDisplayLength = $this->request->getPost('iDisplayLength');
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $sLimit = ' LIMIT ' . (int)$_POST['iDisplayStart'] . ', ' . (int)$_POST['iDisplayLength'];
        }
        if(trim($this->where) != null) {
            $q_where = ' WHERE '. $this->where;
        }
        else {
            $q_where = " ";
        }

        $this->sql = $select . $select_columns.' FROM '. $this->table . $inner_join . $left_join . $q_where . $order . $sLimit;
        $this->total_record_sql = 'SELECT '.$select_columns.' FROM '. $this->table . $inner_join . $left_join . $q_where;
        return $this;
    }

    public function execute(): Datatable
    {
        $result_sql = $this->db->fetchAll($this->sql,Db::FETCH_NUM);
        $this->iCnt = $this->db->query($this->total_record_sql)->numRows();
        $aMembers = $result_sql;
        $aaData = array();
        for ($i = 0; $i < count($aMembers); $i++) {
            foreach ($aMembers[$i] as $key => $value) {
                if (!is_numeric($key)) {
                    unset($aMembers[$i][$key]);
                }
            }
            $aaData[] = $aMembers[$i];
        }
        $this->aaData = $aaData;
        return $this;
    }

    public function result(): array
    {
        $iTotal = $this->getTotalResults();
        $rResult = $this->aaData;
        $aaData = $this->aaData;
        foreach($rResult as $row_key => $row_val)
        {
            $aaData[$row_key] = ($this->checkmDataprop())? $row_val : array_values($row_val);

            foreach($this->add_column as $field => $val) {
                if($this->checkmDataprop()) {
                    $aaData[$row_key][$field] = $this->execReplace($val, $aaData[$row_key]);
                }
                else {
                    $aaData[$row_key][] = $this->execReplace($val, $aaData[$row_key]);
                }
            }


            foreach($this->edit_column as $modkey => $modval) {
                foreach($modval as $val) {
                    $aaData[$row_key][($this->checkmDataprop())? $modkey : array_search($modkey, $this->columns)] = $this->execReplace($val, $aaData[$row_key]);
                }
            }

            if(!$this->checkmDataprop())
                $aaData[$row_key] = array_values($aaData[$row_key]);

        }
        $this->select           = array();
        $this->columns          = array();
        $this->qwhere           = array();
        $this->and_where        = array();
        $this->or_where         = array();
        $this->order_columns    = array();
        $this->limit            = array();
        $this->unset_column     = array();
        $this->add_column       = array();
        $this->edit_column      = array();
        $this->search_column    = array();
        $this->inner_join       = array();
        $this->left_join        = array();
        $this->distinct = false;
        $this->sql              = null;
        $this->total_record_sql = null;
        $this->where = null;
        $this->order = null;
        $this->order_desc = null;
        $this->order_asc = null;
        $this->table = null;
        $this->aaData = null;
        $this->iCnt = null;
        return array(
            'sEcho'                => intval($this->request->getPost('sEcho')),
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iTotal,
            'aaData'               => $aaData,
        );
    }

    public function getTotalResults()
    {
        return $this->iCnt;
    }

    private function checkmDataprop(): bool
    {
        if(!$this->request->get('mDataProp_0'))
            return false;

        for($i = 0; $i < intval($this->request->get('iColumns')); $i++)
            if(!is_numeric($this->request->get('mDataProp_' . $i)))
                return true;

        return false;
    }

    private function execReplace($custom_val, $row_data)
    {
        if(isset($custom_val['replacement']) && is_array($custom_val['replacement'])) {
            foreach($custom_val['replacement'] as $key => $val) {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

                if(preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1])) {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    foreach($args as $args_key => $args_val) {
                        $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        $args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->checkmDataprop())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
                    }

                    $replace_string = call_user_func_array($func, $args);
                }
                else if(in_array($sval, $this->columns)) {
                    $replace_string = $row_data[($this->checkmDataprop())? $sval : array_search($sval, $this->columns)];
                }
                else {
                    $replace_string = $sval;
                }

                $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
            }
        }


        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if(preg_match_all("/$regexp/siU", $custom_val['content'], $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                // $match[2] = link address
                // $match[3] = link text
                $custom_val['content'] = str_replace($match[2], $this->uri->url_encrypt($match[2]), $custom_val['content']);
            }
        }

        $regexp2 = "<a\s[^>]*data-url=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if(preg_match_all("/$regexp2/siU", $custom_val['content'], $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $custom_val['content'] = str_replace($match[2], $this->uri->url_encrypt($match[2]), $custom_val['content']);
            }
        }


        //<a class="'.$class['a'].'" onclick="javascript:confirm
        if(preg_match('/^ <a.*?onclick=(["\'])(.*?)\1.*$/', $custom_val['content'], $data_link)) {
            $new_data = $this->env->getStringBetween($data_link['2'],'(\'','\',\'');

            $custom_val['content'] = str_replace($new_data, $this->uri->url_encrypt($new_data), $custom_val['content']);
        }

        return $custom_val['content'];
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

            if($balance < 1) {
                $retval[] = implode(',', $hold);
                $hold = array();
                $balance = 0;
            }
        }

        if(count($hold) > 0)
            $retval[] = implode(',', $hold);

        return $retval;
    }

    private function balanceChars($str): int
    {
        $openCount = substr_count($str, '(');
        $closeCount = substr_count($str, ')');
        return $openCount - $closeCount;
    }

    protected function getFiltering()
    {
        $sSearch = $this->request->getPost('sSearch','string');
        $sSearch = rtrim($sSearch, '|');
        $sWhere = "";
        if(trim($sSearch) != null) {
            if(!empty($this->search_column)) {
                foreach($this->search_column as $value) {
                    if($sWhere == null) {
                        $sWhere = "$value REGEXP '$sSearch'" ;
                    }
                    else {
                        $sWhere = $sWhere . " OR $value REGEXP '$sSearch'" ;
                    }
                }
            }
            else {
                foreach($this->columns as $value) {
                    if($sWhere == null) {
                        $sWhere = "$value REGEXP '$sSearch'" ;
                    }
                    else {
                        $sWhere = $sWhere . " OR $value REGEXP '$sSearch'" ;
                    }
                }
            }

            if($this->where == null) {
                $this->where = " ($sWhere)";
            }
            else {
                $this->where = " $this->where AND ($sWhere)";
            }
        }
    }

    private function checkSearchableColumns($column): bool
    {
        $status = false;
        if(!empty($this->search_column)) {
            foreach($this->search_column as $value) {
                echo '<pre>';
                echo $column.'\n';
                echo $value;

                if($column == $value) {
                    $status = true;
                }
            }
        }

        return $status;
    }

    public function orderableColumn($columns): Datatable
    {
        $this->order_columns = $columns;
        return $this;
    }

    private function checkOrderableColumns($column): bool
    {
        if($this->order_columns != null) {
            foreach($this->order_columns as $value) {
                if(trim($value) == trim($column)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    protected function getOrdering()
    {
        $sOrder_col = $this->request->getPost('iSortCol_0');
        $sOrder_type = $this->request->getPost('sSortDir_0');

        $order = "";
        if($sOrder_col == "0") {
            if($this->order_desc != null) {
                $order = " ORDER BY $this->order_desc DESC";
            }

            if($this->order_asc != null) {
                $order = " ORDER BY $this->order_asc ASC";
            }
        }
        else {
            if($sOrder_col != null) {
                $order_col = $this->columns[$sOrder_col];
                if($sOrder_type == "asc") {
                    if($this->checkOrderableColumns($order_col))
                        $order = " ORDER BY $order_col ASC";
                }
                else {
                    if($this->checkOrderableColumns($order_col))
                        $order = " ORDER BY $order_col DESC";
                }
            }
        }


        $this->order = $order;

    }

    public function buttons($url, $actions, $class, $name, $target = null, $data_params = null, $data_id = null): string
    {
        switch($actions) {
            case "modal":
                $buttons = ' <a class="'.$class['a'].' btn btn-sm-block btn-xs-block" data-id="'. $data_id .'" data-target="#'. $target .'" data-url="'.$url.'" data-toggle="modal"><i class="'. $class['i'] .'"></i> '.$name .'</a>';
                break;
            case "ajax":
                $buttons = ' <a class="'.$class['a'].' btn btn-sm-block btn-xs-block" target="'. $target .'" data-url="'.$url.'" data-id="confirm"><i class="'. $class['i'] .'"></i> '.$name .'</a>';
                break;
            default:
                $buttons = ' <a class="'.$class['a'].' btn btn-sm-block btn-xs-block" target="'. $target .'" href="'.$url.'" '. $data_params .'><i class="'. $class['i'] .'"></i> '.$name .'</a>';
                break;
        }

        return $buttons;
    }
}