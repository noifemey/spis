<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waitlist_model extends CI_Model {

	public function getAllWaitlist($select = "*", $condition = array(), $or_condition = "", $like = array(), $offset = 0, $order = array(), $limit = 10)
    {
        $this->db->select($select);
		$this->db->from("tblwaitinglist");
		
        $this->db->limit($limit, $offset);
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
	
	public function getWaitlistData($select,$condition,$table,$join,$order,$type){
        $this->db->select($select);
		$this->db->from($table);
        if(!empty($condition)){ $this->db->where($condition); }
        if(!empty($join)){
            $this->db->join($join['table'],$join['oncol'],$join['type']);
        }
        if(!empty($order)){
            $this->db->order_by($order['col'],$order['order_by']);
        }

		$query = $this->db->get();
		if ($query->num_rows()) {

			if ($type =="row") {
				return $query->row();
			}elseif($type =="count_row"){
				return $query->num_rows();
			}elseif($type =="is_array") {
				return $query->result_array();
			}else{
				return $query->result();
			}
		}
		return null;
	}

	public function updateStatus($w_id = "", $data = array()){
		if(!empty($data) && !empty($w_id)) {
			$this->db->where("w_id",$w_id);
			$result = $this->db->update("tblwaitinglist", $data);
		}else{
			return null;
		}
	}
}