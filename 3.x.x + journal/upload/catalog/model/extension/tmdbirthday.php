<?php
class ModelExtensionTmdbirthday extends Model {
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
