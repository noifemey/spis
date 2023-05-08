<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {

    public function get_payroll($condition = array())
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
        $query = $this->db->get();

        $this->db->stop_cache();
        $this->db->flush_cache();


        if ($query->num_rows()) {
            return $query->result_array();
        }

        return [];
    }

    public function get_all_general($select= "*" , $condition = array())
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
        // $this->db->order_by("lastname");
        // $this->db->order_by("firstname");
        // $this->db->order_by("middlename");

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