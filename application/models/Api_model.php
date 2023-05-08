<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

	// public function getbarangay($mun_code=array(),$type=""){
	// 	$this->db->select("*");
	// 	$this->db->from("barangays");
	// 	$this->db->where_in("mun_code",$mun_code);
	// 	$query = $this->db->get();
	// 	if ($query->num_rows()) {

	// 		if ($type=="row") {
	// 			return $query->row();
	// 		}elseif($type=="count_row"){
	// 			return $query->num_rows();
	// 		}else{
	// 			return $query->result();
	// 		}

	// 	}
	// 	return array();
	// }

	// public function getmunicipalities($mun_code=array(),$type=""){
	// 	$this->db->select("*");
	// 	$this->db->from("municipalities");
	// 	$this->db->where_in("prov_code",$mun_code);
	// 	$query = $this->db->get();
	// 	if ($query->num_rows()) {
	// 		if ($type=="row") {
	// 			return $query->row();
	// 		}elseif($type=="count_row"){
	// 			return $query->num_rows();
	// 		}else{
	// 			return $query->result();
	// 		}

	// 	}
	// 	return array();
	// }
	
	public function getProvinces($condition = array(), $offset = 0, $order = array(), $limit = ""){
		$this->db->start_cache();
        $this->db->select('prov_code,prov_name');
		$this->db->from("tblprovinces");
		
		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if(is_array($value)){ $this->db->where_in($key,$value); }
				else{ $this->db->where($key,$value);}
			}
        }
        if($limit != ""){ $this->db->limit($limit, $offset);}
        
        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();
        $this->db->stop_cache();
        $this->db->flush_cache();

        if ($query->num_rows()) {
			$provinces = $query->result_array();
			//$provname_list = array_column($provinces, 'prov_name','prov_code');
            return $provinces;
        }

        return array();
	}

	public function getMunicipalities($condition = array(), $offset = 0, $order = array(), $limit = ""){
		$this->db->start_cache();
        $this->db->select('prov_code,mun_code,mun_name');
		$this->db->from("tblmunicipalities");
		
		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if(is_array($value)){ $this->db->where_in($key,$value); }
				else{ $this->db->where($key,$value);}
			}
        }
        if($limit != ""){ $this->db->limit($limit, $offset);}
        
        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();
        $this->db->stop_cache();
        $this->db->flush_cache();

        if ($query->num_rows()) {
			$municipalities = $query->result_array();
			//$munname_list = array_column($municipalities, 'mun_name','mun_code');
            return $municipalities;
        }

        return array();
	}

	public function getBarangays($condition = array(), $offset = 0, $order = array(), $limit = ""){
		$this->db->start_cache();
        $this->db->select('prov_code,mun_code,bar_code,bar_name');
		$this->db->from("tblbarangays");
		
		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if(is_array($value)){ $this->db->where_in($key,$value); }
				else{ $this->db->where($key,$value);}
			}
        }
        if($limit != ""){ $this->db->limit($limit, $offset);}
        
        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();
        $this->db->stop_cache();
        $this->db->flush_cache();

        if ($query->num_rows()) {
			$barangays = $query->result_array();
			//$barname_list = array_column($barangays, 'bar_name','bar_code');
            return $barangays;
        }

        return array();
	}

	public function getLibraries($table = "tblmaritalstatus",$condition = array(), $offset = 0, $order = array(), $limit = ""){
		$this->db->start_cache();
        $this->db->select('*');
		$this->db->from($table);
		
		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if(is_array($value)){ $this->db->where_in($key,$value); }
				else{ $this->db->where($key,$value);}
			}
        }
        if($limit != ""){ $this->db->limit($limit, $offset);}
        
        if (!empty($order)) {
            $this->db->order_by($order['col'], $order['order_by']);
        }
        
        $query = $this->db->get();
        $this->db->stop_cache();
        $this->db->flush_cache();

        if ($query->num_rows()) {
			$libraries = $query->result_array();
			//$libname_list = array_column($libraries, $colname ,$colid);
            return $libraries;
        }
        return array();
	}
}

/* End of file Api_model.php */
/* Location: ./application/models/Api_model.php */