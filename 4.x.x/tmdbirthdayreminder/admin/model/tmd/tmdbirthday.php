<?php
namespace Opencart\Admin\Model\Extension\tmdbirthdayreminder\Tmd;
class Tmdbirthday extends \Opencart\System\Engine\Model {
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tmd_coupon_history` (
		  `tmd_coupon_history_id` int(11) NOT NULL AUTO_INCREMENT,
		  `coupon_id` int(11) NOT NULL,
		  `order_id` int(11) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `amount` decimal(15,4) NOT NULL,
		  `code` text NOT NULL,
		  `date_added` date NOT NULL,
		   PRIMARY KEY (`tmd_coupon_history_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tmd_customer_coupon` (
		  `c_id` int(11) NOT NULL AUTO_INCREMENT,
		  `coupon_id` int(11) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `date_added` date NOT NULL,
		   PRIMARY KEY (`c_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
		
		$this->db->query("ALTER TABLE `".DB_PREFIX."customer` ADD `date_of_birth` date NOT NULL AFTER `store_id`");


		// cron job
		$code='birthdayreminder';
		$description='birthdayreminder';
		$cycle='Day';
		$action='extension/tmdbirthdayreminder/tmd/tmdcronjob';
		$status='1';
		$this->db->query("INSERT INTO `" . DB_PREFIX . "cron` SET `code` = '" . $this->db->escape($code) . "', `description` = '" . $this->db->escape($description) . "', `cycle` = '" . $this->db->escape($cycle) . "', `action` = '" . $this->db->escape($action) . "', `status` = '" . (int)$status . "', `date_added` = NOW(), `date_modified` = NOW()");
		// cron job

		
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tmd_customer_coupon`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tmd_coupon_history`");
		$this->db->query("ALTER TABLE `".DB_PREFIX."customer` DROP `date_of_birth`");

		// cron job
		$code='birthdayreminder';
		$this->db->query("delete from " . DB_PREFIX . "cron  where code = '" . $this->db->escape($code) ."'");
		// cron job
		
	}
	
	public function addTmdCoupon($data,$customer_id) {
		$module_language = $this->config->get('module_tmdbirthday_language');
		$this->load->model('customer/customer');
		$customer_info = $this->model_customer_customer->getCustomer($customer_id);
		
		if(!empty($customer_info['email']) && !empty($module_language[$this->config->get('config_language_id')]['subject']) && !empty($module_language[$this->config->get('config_language_id')]['message'])) {
			$email = $customer_info['email'];
			
			if(!empty($module_language[$this->config->get('config_language_id')]['title'])) {
				$name = $module_language[$this->config->get('config_language_id')]['title'].'('.$data['code'].')';
			} else {
				$name = 'BirthDay'.'('.$data['code'].')';
			}

			$this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name='".$this->db->escape($name)."',code = '" . $this->db->escape($data['code']) . "',type = '" . $this->db->escape($data['dis_type']) . "',discount = '" . (float)$data['dis_value'] . "',total = '" . (float)$data['total_amount'] . "',date_start='".$this->db->escape($data['date_start'])."',date_end='".$this->db->escape($data['date_end'])."',logged = '1',status='1',uses_total = '" . (int)$data['use_coupon'] . "', uses_customer = '" . (int)$data['use_customer'] . "',date_added=NOW()");
			
			$coupon_id = $this->db->getLastId();

			if (!empty($data['products'])) {
				$productids = explode(',', $data['products']);
			} else {
				$productids = $this->config->get('module_tmdbirthday_productids');
			}
			if (isset($productids)) {
				foreach ($productids as $product_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
				}
			}

			if (!empty($data['categories'])) {
				$categoryids = explode(',', $data['categories']);
			} else {
				$categoryids = $this->config->get('module_tmdbirthday_categoryids');
			}
			if (isset($categoryids)) {
				foreach ($categoryids as $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
				}
			}

			$this->db->query("INSERT INTO " . DB_PREFIX . "tmd_customer_coupon SET coupon_id='".(int)$coupon_id."',customer_id='".(int)$customer_id."',date_added=NOW()");
						
			if($data['dis_type']=='P') {
				$dis_type = '%';
			} else {
				$dis_type ='';
			}

			$find = array(
				'{customer}',
				'{coupon_code}',
				'{discount_value}',
				'{total_amount}',
				'{end_date}',
				'{store_name}',
			);
			$replace = array(
				'customer' 		=> $customer_info['firstname'].' '.$customer_info['lastname'],
				'coupon_code' 	=> $data['code'],
				'discount_value'=> $data['dis_value'].' '.$dis_type,
				'total_amount'	=> $this->currency->format($data['total_amount'],$this->config->get('config_currency')),
				'end_date' 		=> $data['date_end'],
				'store_name' 	=> $this->config->get('config_name'),
			);

			$tmdsubject = $module_language[$this->config->get('config_language_id')]['subject'];
			$tmdmessage = html_entity_decode($module_language[$this->config->get('config_language_id')]['message']);
						
	     	$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsubject))));
			$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdmessage))));

			if(VERSION>='4.0.2.0'){
				if ($this->config->get('config_mail_engine')) {
					$mail_option = [
						'parameter'     => $this->config->get('config_mail_parameter'),
						'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
						'smtp_username' => $this->config->get('config_mail_smtp_username'),
						'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
						'smtp_port'     => $this->config->get('config_mail_smtp_port'),
						'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
					];

					$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
					$mail->setTo($email);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
					$mail->setSubject($subject.' '.$customer_info['firstname'].' '.$customer_info['lastname']);
					$mail->setHtml(html_entity_decode($message));
					$mail->send();
				}
			}else{
				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
			
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject.' '.$customer_info['firstname'].' '.$customer_info['lastname']);
			$mail->setHtml(html_entity_decode($message));
			$mail->send();
			
			}
			

			return $coupon_id;
		}
	}

	public function addDefaultCoupon($data) {
		$data['code'] = $data['module_tmdbirthday_coupons'];
		$data['date_start'] = $data['module_tmdbirthday_start'];
		$data['date_end'] = $data['module_tmdbirthday_end'];
		$data['dis_type'] = $data['module_tmdbirthday_dis_type'];
		$data['dis_value'] = $data['module_tmdbirthday_dis_value'];
		$data['total_amount'] = $data['module_tmdbirthday_total_amount'];
		$data['use_coupon'] = $data['module_tmdbirthday_use_coupon'];
		$data['use_customer'] = $data['module_tmdbirthday_use_customer'];
		
		$module_language = $this->config->get('module_tmdbirthday_language');
		if(!empty($module_language[$this->config->get('config_language_id')]['title'])) {
			$name = $module_language[$this->config->get('config_language_id')]['title'].'('.$data['code'].')';
		} else {
			$name = 'BirthDay'.'('.$data['code'].')';
		}

		$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($data['code']) . "' AND status = '1'")->row;

		if (empty($coupon_query['coupon_id'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name='".$this->db->escape($name)."',code = '" . $this->db->escape($data['code']) . "',type = '" . $this->db->escape($data['dis_type']) . "',discount = '" . (float)$data['dis_value'] . "',total = '" . (float)$data['total_amount'] . "',date_start='".$this->db->escape($data['date_start'])."',date_end='".$this->db->escape($data['date_end'])."',logged = '1',status='1',uses_total = '" . (int)$data['use_coupon'] . "', uses_customer = '" . (int)$data['use_customer'] . "',date_added=NOW()");
			
			$coupon_id = $this->db->getLastId();
			
			if (isset($data['module_tmdbirthday_productids'])) {
				foreach ($data['module_tmdbirthday_productids'] as $product_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
				}
			}
			
			if (isset($data['module_tmdbirthday_categoryids'])) {
				foreach ($data['module_tmdbirthday_categoryids'] as $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
				}
			}
			return $coupon_id;
		} else {
			return $coupon_query['coupon_id'];
		}
	}
	
	public function getCustomers($data = array()) {
		$dm = date('m-d');
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";
			
		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.date_of_birth!='0000-00-00'";
		
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE date_of_birth!='0000-00-00'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getDateByCustomers($date_of_birth) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";
		$sql .= " WHERE DATE_FORMAT(c.date_of_birth, '%m-%d') = DATE_FORMAT('".$date_of_birth."', '%m-%d') and cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		$query = $this->db->query($sql);
	
		return $query->rows;
	}

	public function getDOBCustomer($date) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";
			
		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' and c.date_of_birth='".$date."'";
				
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getApplyCoupon($customer_id) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tmd_customer_coupon tc LEFT JOIN ".DB_PREFIX."coupon c ON (tc.coupon_id = c.coupon_id) WHERE customer_id='".(int)$customer_id."' AND ((c.date_start = '0000-00-00' OR c.date_start < NOW()) AND (c.date_end = '0000-00-00' OR c.date_end > NOW()))";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getExitsCoupon($coupon_id,$date_start,$date_end) {
		$sql = "SELECT * FROM " . DB_PREFIX . "coupon WHERE coupon_id='".(int)$coupon_id."' AND date_start <= '".$this->db->escape($date_start)."' AND date_end >= '".$this->db->escape($date_end)."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getExitsCustomerCoupon($customer_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "tmd_customer_coupon WHERE customer_id='".(int)$customer_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function addCustomer(array $data):void {
      	$query = $this->db->query("SELECT * FROM ".DB_PREFIX."customer ORDER BY customer_id DESC ");
   		if (isset($data['date_of_birth'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET date_of_birth = '" . $data['date_of_birth'] . "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
		}	
    }

    public function editCustomer(array $data):void {
   		if (isset($data['date_of_birth'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET date_of_birth = '" . $data['date_of_birth'] . "' WHERE customer_id = '" . (int)$data['customer_id'] . "'");
		}
    }
}
