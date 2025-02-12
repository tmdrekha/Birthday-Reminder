<?php
namespace Opencart\catalog\Controller\Extension\tmdbirthdayreminder\Tmd;
class Tmdcronjob extends \Opencart\System\Engine\Controller {

	public function index() {
		$module_status	= $this->config->get('module_tmdbirthday_status');
		$tmdcronjobstatus	= $this->config->get('module_tmdbirthday_cronjobstatus');
		if($tmdcronjobstatus== 1 && $module_status==1){
			$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');

			$totals = 0 ;

			$customers = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getDOBCustomer();

			foreach($customers as $customer) {
				$module_language = $this->config->get('module_tmdbirthday_language');
				if(!empty($customer['email']) && !empty($module_language[$this->config->get('config_language_id')]['subject']) && !empty($module_language[$this->config->get('config_language_id')]['message'])) {
					$totals++;
					
					$tmdsubject	= $module_language[$this->config->get('config_language_id')]['subject'];
					$tmdmessage	= html_entity_decode($module_language[$this->config->get('config_language_id')]['message']);
					
					$date_end 	= $this->config->get('module_tmdbirthday_end');
					$coupons 	= $this->config->get('module_tmdbirthday_coupons');
					$dis_type 	= $this->config->get('module_tmdbirthday_dis_type');
					$dis_value 	= $this->config->get('module_tmdbirthday_dis_value');
					$total_amount 	= $this->config->get('module_tmdbirthday_total_amount');
					
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
						'customer' 		=> $customer['firstname'].' '.$customer['lastname'],
						'coupon_code' 	=> $coupons,
						'discount_value'=> $data['dis_value'].' '.$dis_type,
						'total_amount'	=> $this->currency->format($total_amount, $this->config->get('config_currency')),
						'end_date' 		=> $date_end,
						'store_name' 	=> $this->config->get('config_name'),
					);

					$subject = str_replace($find, $replace, $tmdsubject);
					$message = str_replace($find, $replace, $tmdmessage);
					
					if(VERSION>='4.0.2.0') {
						$mail_option = [
							'parameter'     => $this->config->get('config_mail_parameter'),
							'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
							'smtp_username' => $this->config->get('config_mail_smtp_username'),
							'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
							'smtp_port'     => $this->config->get('config_mail_smtp_port'),
							'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
						];
						$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
					} else{
						$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
					}
						
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
		} else {
			echo 'Status Disable';
		}
	}
}