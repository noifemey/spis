<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sp_libraries_model extends CI_Model {

	public function get_living_arrangement($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('id,name,status');
		$this->db->from("tbllivingarrangement");
		
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
            return $query->result();
        }

        return array();
    }



    public function get_house_type($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('id,name,status');
		$this->db->from("tblhousetype");
		
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
            return $query->result();
        }

        return array();
    }


    public function get_marital_status($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('id,name,status');
		$this->db->from("tblmaritalstatus");
		
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
            return $query->result();
        }

        return array();
    }

    public function get_reasons($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('id,name,status');
		$this->db->from("tblinactivereason");
		
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
            return $query->result();
        }

        return array();
    }

    public function get_signatories($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('*');
		$this->db->from("tblsignatories");
		
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
            return $query->result();
        }

        return array();
    }


    public function get_targets($condition = array(), $offset = 0, $order = array(), $limit = "")
    {

    	$this->db->start_cache();

        $this->db->select('t.id,t.mun_code,t.target,t.year,t.semester,t.quarter,m.mun_name,p.prov_name,p.prov_code');
		$this->db->from("tbltarget t");
		$this->db->join('tblmunicipalities m', 'm.`mun_code`=t.`mun_code`', 'inner');
		$this->db->join('tblprovinces p', 'p.`prov_code`=m.`prov_code`', 'LEFT');
		
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

        $this->db->order_by("p.prov_code", "asc");
        $this->db->order_by("t.year", "desc");
        
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result();
        }

        return array();
    }


}