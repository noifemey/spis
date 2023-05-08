<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class cm_model extends CI_Model {

	public function getAllData($table, $select = "*", $condition = array(), $or_condition = "", $like = array(), $offset = 0, $order = array(), $limit = 10)
    {
        $this->db->select($select);
		$this->db->from($table);
		
        //$this->db->limit($limit, $offset);
        if (!empty($like)) {
			$this->db->group_start();
            if (is_array($like['column'])) {
                foreach ($like['column'] as $lk => $lv) {
                    $this->db->or_like($lv, $like['data']);
                }
            } else {
                $this->db->like($like['column'], $like['data']);
			}
			$this->db->group_end();
        }
        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        if (!empty($or_condition)) {
            $this->db->where($or_condition);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result();
        }
        return array();
	}
}