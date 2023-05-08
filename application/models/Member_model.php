<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {

	public function getAllMembers($select = "*", $condition = array(), $or_condition = "", $like = array(), $offset = 0, $order = array(), $limit = 10)
    {
        $this->db->select($select);
		$this->db->from("tblgeneral");
		
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

    public function get_member_payment($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("*");
        $this->db->from("tblpayroll");

        if (!empty($condition)) {
            foreach ($condition as $key => $value) {
                if(is_array($value))
                {
                    $this->db->where_in($key,$value);
                }
                else
                {
                    $this->db->where($key,$value);
                }
            }
            
        }
        $this->db->order_by('year', 'Desc');
        $this->db->order_by('mode_of_payment','Desc');
        $this->db->order_by('period','Desc');

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

	public function getReplacementHistoryOfPensioner($spid,$replace_stat){
		
		// $sql = "SELECT * FROM tblreplace WHERE `$replace_stat` = '$spid'";
        // $query = $this->Main->raw($sql,1);
        
		$qry = array(
			"select" => "*",
			"table" => "tblreplace",
			'type' => "row",
			'order' => array(
				'col' => "r_id",
				'order_by' => "DESC"),
			'condition' => ["$replace_stat" => "$spid"],
		);
		$getReplacer = $this->Main->select($qry);
		
		if($getReplacer){
			return $getReplacer;	
		} else {
			return NULL;
		}
    }
    
    public function memberDetails($select="*",$condition=array(),$type = false){
        $this->db->select($select);
        $this->db->from("tblgeneral");
        $this->db->where($condition);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        }
        return null;
    }
    
    public function paymentDetails($select="*",$condition=array(),$type = false){
        $this->db->select($select);
        $this->db->from("tblpayroll");
        $this->db->where($condition);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        }

        $str = $this->db->last_query();      
        print_r($str);    
        exit;

        return null;
    }
}