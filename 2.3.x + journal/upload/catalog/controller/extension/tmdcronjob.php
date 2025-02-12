<?php
class ControllerExtensionTmdcronjob extends Controller {
	public function index() {
		$tmdcronjobstatus	= $this->config->get('tmdbirthday_cronjobstatus');
		if($tmdcronjobstatus == 1){
			$data['token'] = $this->session->data['token'];
			$this->load->model('account/customer');

			$data['tmdbirthday']=array();
			$date = date('Y-m-d');
			
			$customers = $this->model_account_customer->getDOBCustomer($date);

			foreach($customers as $customer) {
				if($customer['date_of_birth'] == $date){
					$tmdsubject	= $this->config->get('tmdbirthday_subject');
					$tmdmessage	= $this->config->get('tmdbirthday_message');
					$date_end 	= $this->config->get('tmdbirthday_end');
					$coupons 	= $this->config->get('tmdbirthday_coupons');
					$dis_value 	= $this->config->get('tmdbirthday_dis_value');
					$total_amount 	= $this->config->get('tmdbirthday_total_amount');
				
					$find = array(
						'{firstname}',
						'{lastname}',
						'{coupon_code}',
						'{discount_value}',
						'{total_amount}',
						'{end_date}',										
					);
					
					$replace = array(
						'firstname' 	=> $customer['firstname'],
						'lastname' 		=> $customer['lastname'],
						'coupon_code' 	=> $coupons,
						'discount_value'=> $dis_value,
						'total_amount'	=> $total_amount,
						'end_date' 		=> $date_end,
					);

					$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsubject))));
					$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdmessage))));
				
					$mail = new Mail();
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
			
			$total_customers = $this->model_account_customer->getbirthcustomer();
			if(isset($total_customers)) {
				echo $total_customers.' Email Send!';
			}else{
				echo '0 Email Send!';
			}
		} else {
			echo 'Status Disable';
		}
	}
}