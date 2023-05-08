<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Replacement_model extends CI_Model {

    public function get_payroll_list($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("spid,amount,bar_code,mun_code,prov_code,receiver,date_receive,liquidation,mode_of_payment,eligible");
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
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function get_all_yearbenes($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("DISTINCT(spid)");
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
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function get_all_general($condition = array())
    {
        $this->db->start_cache();
        $this->db->select("b_id,connum,lastname,firstname,middlename,extensionname,province, city, barangay,period_mode,quarter_start,year_start, sp_status,inactive_reason_id, sp_inactive_remarks,birthdate,replacer,additional");
        $this->db->from("tblgeneral");

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

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function batchPayment($year,$modepay,$qtrsem,$spids,$data){
        
        //$this->db->group_start();
        // $sp_ids_chunk = array_chunk($spids,500);
        // foreach($sp_ids_chunk as $sp_ids){
        //     $this->db->where("year",$year);
        //     $this->db->where("mode_of_payment",$modepay);
        //     $this->db->where("period",$qtrsem);
        //     $this->db->where_in("spid", $sp_ids);
        //     $result = $this->db->update("tblpayroll", $data);
        // }
        //$this->db->group_end();

        $this->db->where("year",$year);
        $this->db->where("mode_of_payment",$modepay);
        $this->db->where("period",$qtrsem);
        $this->db->where_in("spid", $spids);
        $result = $this->db->update("tblpayroll", $data);
        return $result;
    }

    public function getReplacementHistoryOfPensioner($spid,$replace_stat){
		
		// $sql = "SELECT * FROM tblreplace WHERE `$replace_stat` = '$spid'";
		// $query = $this->Main->raw($sql,1);
		
		// if($query){
		// 	return $query;	
		// } else {
		// 	return NULL;
        // }
        
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

    public function get_all_waitlist($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("*");
        $this->db->from("tblwaitinglist");

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

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function get_all_replacement($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("*");
        $this->db->from("tblreplace");

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

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();

        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

}