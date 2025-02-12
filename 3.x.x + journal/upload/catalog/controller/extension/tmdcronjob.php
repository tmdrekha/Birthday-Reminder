<?php
class ControllerExtensionTmdcronjob extends Controller {
	public function index() {
		$tmdcronjobstatus	= $this->config->get('module_tmdbirthday_cronjobstatus');
		$module_status		= $this->config->get('module_tmdbirthday_status');
		if($tmdcronjobstatus== 1 && $module_status==1){
			$this->load->model('extension/tmdbirthday');
			
			$totals = 0 ;

			$customers = $this->model_extension_tmdbirthday->getDOBCustomer();
			
			foreach($customers as $customer) {
				$module_language = $this->config->get('module_tmdbirthday_language');
				if(!empty($customer['email']) && !empty($module_language[$this->config->get('config_language_id')]['subject']) && !empty($module_language[$this->config->get('config_language_id')]['message'])) {
					$totals++;

					$tmdsubject = $module_language[$this->config->get('config_language_id')]['subject'];
					$tmdmessage = html_entity_decode($module_language[$this->config->get('config_language_id')]['message']);

					$date_end 	= $this->config->get('module_tmdbirthday_end');
					$coupons 	= $this->config->get('module_tmdbirthday_coupons');
					$dis_value 	= $this->config->get('module_tmdbirthday_dis_value');
					$total_amount= $this->config->get('module_tmdbirthday_total_amount');
				
					$find = array(
						'{customer}',
						'{coupon_code}',
						'{discount_value}',
						'{total_amount}',
						'{end_date}',										
						'{store_name}',										
					);
					
					$replace = array(
						'customer' 		=> $customer['firstname'].' '.$customer['lastname'],
						'coupon_code' 	=> $coupons,
						'discount_value'=> $dis_value,
						'total_amount'	=> $total_amount,
						'end_date' 		=> $date_end,
						'store_name' 	=> $this->config->get('config_name'),
					);

					$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsubject))));
					$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdmessage))));
				
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($customer['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
					$mail->setSubject($subject);
					$mail->setHtml(html_entity_decode($message));
					$mail->send();
				}
			}
			echo $totals.' Email Send!';
		}else {
			echo 'Status Disable';
		}
	}
}
