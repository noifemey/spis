<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Model
{

	private $dromic;


	public function __construct()
	{
		parent::__construct();
		//$this->dromic = $this->load->database('dromic', TRUE);
	}

	public function insert($table, $data, $return = false, $dromic_db = false)
	{
		$db_used = (!$dromic_db) ? $this->db : $this->dromic;

		if ($return) {
			$lastid = "";
			$result =  $db_used->insert($table, $data);
			if ($result) {
				$lastid =  $db_used->insert_id();
			}
			return array('success' => $result, 'lastid' => $lastid);
		}
		return $db_used->insert($table, $data);
	}


	public function insertbatch($table = "", $data = array())
	{
		return $this->db->insert_batch($table, $data);
	}

	public function count($table = "", $condition = array())
	{
		$this->db->select("count(*) as total");
		$this->db->from($table);
		if (!empty($condition)) {
			// $this->db->where($condition);
			foreach ($condition as $ck => $cv) {
				if (is_array($cv)) {
					$this->db->where_in($ck, $cv);
				} else {
					$this->db->where($ck, $cv);
				}
			}
		}
		return $this->db->get()->row()->total;
	}

	public function select($data, $like = array(), $group = false)
	{
		$this->db->select($data['select']);
		$this->db->from($data['table']);
		// if (!empty($data['condition'])) {
		// 	$this->db->where($data['condition']);
		// }

		if (!empty($data['condition'])) {
			if (is_array($data['condition'])) {

				foreach ($data['condition'] as $key => $value) {

					if (is_array($value)) {
						$this->db->where_in($key, $value);
					} else {
						$this->db->where($key, $value);
					}
				}
			} else {
				$this->db->where($data['condition']);
			}
		}

		if (!empty($data['limit'])) {

			$offset = 0;
			if (!empty($data['offset'])) {
				$offset = $data['offset'];
			}
			$this->db->limit($data['limit'], $offset);
		}

		if (!empty($like)) {
			if ($group) {
				$this->db->group_start();
			}
			if (is_array($like['column'])) {
				foreach ($like['column'] as $lk => $lv) {
					$this->db->or_like($lv, $like['data']);
				}
			} else {

				$this->db->like($like['column'], $like['data']);
			}
			if ($group) {
				$this->db->group_end();
			}
		}

		if (!empty($data['group_by'])) {
			$this->db->group_by($data['group_by']);
		}
		if (!empty($data['order'])) {
			$this->db->order_by($data['order']['col'], $data['order']['order_by']);
		}

		$query = $this->db->get();
		if ($query->num_rows()) {

			if ($data['type'] == "row") {
				return $query->row();
			} elseif ($data['type'] == "count_row") {
				return $query->num_rows();
			} elseif ($data['type'] == "row_array") {
				return $query->row_array();
			} elseif ($data['type'] == "result_array") {
				return $query->result_array();
			} else {
				return $query->result();
			}
		}
		return array();
	}

	public function delete($table, $condition)
	{
		foreach ($condition as $key => $value) {
			if (!is_array($value)) {
				$this->db->where($key, $value);
			} else {
				$this->db->where_in($key, $value);
			}
		}


		return $this->db->delete($table);
	}

	public function update($table, $condition, $data, $return = "")
	{
		if (is_array($return)) {
			$this->db->where_in($return['col'], $condition);
		} else {
			$this->db->where($condition);
		}
		$r = $this->db->update($table, $data);
		if ($r) {
			return  array('success' => true);
		}
	}

	public function updatebatch($table = "", $data, $id = "")
	{
		return $this->db->update_batch($table, $data, $id);
	}

	public function raw($query, $row = false, $type = "")
	{
		$query = $this->db->query($query);
		if ($type != "update") {
			if ($query->num_rows()) {

				if ($row) {
					return $query->row();
				}
				return $query->result();
			}
			return null;
		}
	}

	public function searchlistahan($select = "*", $condition = array(), $limit = 30, $type = true)
	{
		if ($type) {
			$bev = $this->load->database('bev', true);
			$connected = $bev->initialize();
			$bev->select($select);
			$bev->from('nhts_db_complete');
			if (!empty($condition)) {
				$bev->where($condition);
			}

			// if (!empty($condition['or'])) {
			// 	foreach ($condition['or'] as $or) {
			// 		$or = $bev->escape_like_str($or);
			// 		$bev->or_like($or);

			// 	}
			// }
			// if (!empty($condition['and'])) {
			// 	foreach ($condition['and'] as $con) {
			// 		$bev->where($con);
			// 	}
			// }
			if ($limit >= 1) {
				$bev->limit($limit, 0);
			} else {
				$bev->limit(30, 0);
			}

			$query = $bev->get();

			if ($query) {
				return $query->result();
			}

			return null;
		} else {
			$this->db->select($select);
			$this->db->from('nhts_local');
			if (!empty($condition)) {
				$this->db->where($condition);
			}

			if ($limit >= 1) {
				$this->db->limit($limit, 0);
			} else {
				$this->db->limit(30, 0);
			}

			$query = $this->db->get();

			if ($query) {
				return $query->result();
			}

			return null;
			// return array('result' => false, 'message' => "Your connection lost!");
		}
	}

	public function emptyData($data = array())
	{
		if (!empty($data)) {
			foreach ($data as $value) {
				$this->db->from($value);
				$this->db->truncate();
			}
		}
	}



	//START GET LIBRARIES

	public function get_all_provinces($condition = array(), $offset = 0, $order = array(), $limit = "")
	{

		$this->db->start_cache();

		$this->db->select('prov_code,prov_name');
		$this->db->from("tblprovinces");

		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		if ($limit != "") {
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
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		if ($limit != "") {
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

	public function getBarangays($condition = array(), $offset = 0, $order = array(), $limit = "")
	{
		$this->db->start_cache();
		$this->db->select('prov_code,mun_code,bar_code,bar_name');
		$this->db->from("tblbarangays");

		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}
		if ($limit != "") {
			$this->db->limit($limit, $offset);
		}

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

	public function getrelationships($condition = array())
	{
		$this->db->start_cache();

		$this->db->select("relid,relname");
		$this->db->from("tblrelationships");

		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
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

	public function getreasonforrep($condition = array())
	{
		$this->db->start_cache();

		$this->db->select("id,name");
		$this->db->from("tblinactivereason");

		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
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

	public function getLibraries($table = "tblmaritalstatus", $condition = array(), $offset = 0, $order = array(), $limit = "")
	{
		$this->db->start_cache();
		$this->db->select('*');
		$this->db->from($table);

		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}
		if ($limit != "") {
			$this->db->limit($limit, $offset);
		}

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

	public function updateReqForm($id,$reqDetails,$status,$actionTaken)
	{
		$data = array(
			'issue_details' => $reqDetails,
			'status' => $status,
			'action_taken' => $actionTaken
		);
		$this->db->where('id', intval($id));
		return $this->db->update('tblchangerequest',$data);
	}

	//END GET LIBRARIES
}

/* End of file Main.php */
/* Location: ./application/models/Main.php */
