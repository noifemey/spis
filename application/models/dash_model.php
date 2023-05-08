<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class dash_model extends CI_Model {
    
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

        $this->db->order_by("prov_name");

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

        $this->db->select("spid,prov_code,mun_code,bar_code");
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

    public function get_all_general($select = "connum,gender" ,$condition = array())
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

    public function get_all_waitlist()
    {
        $this->db->start_cache();

        $this->db->select("prov_code");
        $this->db->from("tblwaitinglist");
        $this->db->where("archived",0);
        $this->db->where("priority",1);

        // if (!empty($condition)) {
        //     foreach ($condition as $key => $value) {
        //         if(is_array($value))
        //         {
        //             $this->db->where_in($key,$value);
        //         }
        //         else
        //         {
        //             $this->db->where($key,$value);
        //         }
        //     }
            
        // }

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
}