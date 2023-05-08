<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model {

	public function get_all_provinces($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('prov_code,prov_name');
		$this->db->from("tblprovinces");
		
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

        if($limit != "")
        {
        	$this->db->limit($limit, $offset);
        }
        

        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return array();
    }

    public function get_all_municipalities($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

        $this->db->start_cache();

        $this->db->select('prov_code,mun_code,mun_name');
        $this->db->from("tblmunicipalities");
        
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

        if($limit != "")
        {
            $this->db->limit($limit, $offset);
        }
        

        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return array();
    }

    public function get_total_served($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("spid,prov_code,mun_code,bar_code,liquidation");
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
        
        $this->db->order_by('mun_code');

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function get_all_general($select = "connum,gender", $condition = array())
    {
        $this->db->start_cache();

        $this->db->select($select);
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

    public function get_all_targets($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

        $this->db->start_cache();

        $this->db->select('mun_code,year,target');
        $this->db->from("tbltarget");
        
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

        if($limit != "")
        {
            $this->db->limit($limit, $offset);
        }
        

        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return array();
    }

    public function get_all_waitlist($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("prov_code,mun_code,priority,sent_to_co");
        $this->db->from("tblwaitinglist");
        $this->db->where("archived",0);

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

    public function get_all_inactive($condition = array())
    {
        $this->db->start_cache();

        $this->db->select("COUNT(connum) as total, city, inactive_reason_id");
        $this->db->from("tblgeneral");
        $this->db->where("inactive_reason_id IS NOT NULL");
        $this->db->where("inactive_reason_id !=", 0);
        $this->db->where("sp_status", 'Inactive');

        if (!empty($condition)) {
            if(isset($condition['year']) && $condition['year'] != ""){
                $this->db->where("YEAR(sp_status_inactive_date)", $condition['year']);
            }

            if(isset($condition['period']) && $condition['period'] != ""){
                $period_month = ($_POST['period'] == 1) ? 'BETWEEN 1 AND 6' : 'BETWEEN 7 AND 12';
                $this->db->where("MONTH(sp_status_inactive_date) $period_month", NULL, FALSE);
            }

            if(isset($condition['month']) && $condition['month'] != ""){
                $this->db->where("MONTH(sp_status_inactive_date)", $condition['month']);
            }
        }

        $this->db->group_by('city, inactive_reason_id');

        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }


}