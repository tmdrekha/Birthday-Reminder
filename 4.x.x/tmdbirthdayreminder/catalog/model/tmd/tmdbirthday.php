<?php
namespace Opencart\Catalog\Model\Extension\tmdbirthdayreminder\Tmd;
class Tmdbirthday extends \Opencart\System\Engine\Model {

	public function addCustomer(array $data):void {
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."customer ORDER BY customer_id DESC ");
		if (isset($data['date_of_birth'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET date_of_birth = '" . $data['date_of_birth'] . "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
		}
	}

	public function editCustomer(array $data):void {
		if (isset($data['date_of_birth'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customer_id = '" . $customer_id . "', date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "'");
		}
	}

	public function getDOBCustomer() {
		$currentDate = date('Y-m-d'); 
		$xdays	= $this->config->get('module_tmdbirthday_sendemaildays');
		$days = ' +'.$xdays.' days'; 
		$futureDate = date('Y-m-d', strtotime($currentDate . $days));
		$date = date('d', strtotime($futureDate));  
		$month = date('m', strtotime($futureDate)); 
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer` WHERE DAY(date_of_birth) = '".$date."'  AND MONTH(date_of_birth) ='".$month."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
}
