<?php
class ControllerExtensionModuleTmdbirthday extends Controller {
	private $error = array();

	public function install() {
		$this->load->model('extension/tmdbirthday');
		$this->model_extension_tmdbirthday->install();
	}
	
	public function uninstall() {
		$this->load->model('extension/tmdbirthday');
		$this->model_extension_tmdbirthday->uninstall();
	}
	
	public function index() {
		$this->load->language('extension/module/tmdbirthday');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('extension/tmdbirthday');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('tmdbirthday', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if ($this->request->get['status']==1) {
				$this->response->redirect($this->url->link('extension/module/tmdbirthday', 'token=' . $this->session->data['token'] . '&type=module', true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
			}
		}

		$data['heading_title'] 		= $this->language->get('heading_title');

		$data['text_heading'] 		= $this->language->get('text_heading');
		$data['text_edit'] 			= $this->language->get('text_edit');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['text_yes'] 			= $this->language->get('text_yes');
		$data['text_no'] 			= $this->language->get('text_no');
		$data['text_loading'] 		= $this->language->get('text_loading');
		
		$data['tab_setting']    	= $this->language->get('tab_setting');
		$data['tab_giftmail']    	= $this->language->get('tab_giftmail');
		$data['tab_calendar']    	= $this->language->get('tab_calendar');
		$data['tab_cronjon']    	= $this->language->get('tab_cronjon');
		
		$data['column_apply'] 	= $this->language->get('column_apply');
		$data['column_name'] 	= $this->language->get('column_name');
		$data['column_email'] 	= $this->language->get('column_email');
		$data['column_dob'] 	= $this->language->get('column_dob');
		$data['column_group'] 	= $this->language->get('column_group');
		$data['column_status'] 	= $this->language->get('column_status');
		$data['column_date'] 	= $this->language->get('column_date');
		$data['column_action'] 	= $this->language->get('column_action');
		
		$data['entry_status'] 		= $this->language->get('entry_status');
		$data['entry_displaycart'] 	= $this->language->get('entry_displaycart');
		$data['entry_displaycheck'] = $this->language->get('entry_displaycheck');
		$data['entry_size'] 		= $this->language->get('entry_size');
		$data['entry_height'] 		= $this->language->get('entry_height');
		$data['entry_width'] 		= $this->language->get('entry_width');
		$data['entry_heading'] 		= $this->language->get('entry_heading');
		$data['entry_add_to_cart'] 	= $this->language->get('entry_add_to_cart');
		
		
		$data['entry_code'] 	= $this->language->get('entry_code');
		$data['entry_date'] 	= $this->language->get('entry_date');
		$data['entry_dis_type'] 	= $this->language->get('entry_dis_type');
		$data['entry_dis_value'] 	= $this->language->get('entry_dis_value');
		$data['entry_total'] 	= $this->language->get('entry_total');
		$data['entry_use_coupon'] 	= $this->language->get('entry_use_coupon');
		$data['entry_use_customer'] 	= $this->language->get('entry_use_customer');
		$data['entry_subject'] 	= $this->language->get('entry_subject');
		$data['entry_message'] 	= $this->language->get('entry_message');
		$data['entry_start'] 	= $this->language->get('entry_start');
		$data['entry_end'] 	= $this->language->get('entry_end');
		$data['entry_cronstatus'] 	= $this->language->get('entry_cronstatus');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_savestay'] = $this->language->get('button_savestay');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tmdbirthday', 'token=' . $this->session->data['token'], true)
		);
		
		$data['dateformates']= array();
		$data['dateformates'][] = array(
			'text'  => $this->language->get('text_f1'),
			'value' => 'YYYY-MM-DD'
		);
		$data['dateformates'][] = array(
			'text'  => $this->language->get('text_f2'),
			'value' => 'MM-DD-YYYY'
		);
		$data['dateformates'][] = array(
			'text'  => $this->language->get('text_f3'),
			'value' => 'DD-MM-YYYY'
		);
		
		$data['types']= array();
		$data['types'][] = array(
			'text'  => $this->language->get('text_percent'),
			'value' => 'P'
		);
		$data['types'][] = array(
			'text'  => $this->language->get('text_fixe'),
			'value' => 'F'
		);
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['action'] = $this->url->link('extension/module/tmdbirthday', 'token=' . $this->session->data['token'], true);
		$data['staysave'] = $this->url->link('extension/module/tmdbirthday', '&status=1&token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['tmdbirthday_status'])) {
			$data['tmdbirthday_status'] = $this->request->post['tmdbirthday_status'];
		} else {
			$data['tmdbirthday_status'] = $this->config->get('tmdbirthday_status');
		}
		
		if (isset($this->request->post['tmdbirthday_coupons'])) {
			$data['tmdbirthday_coupons'] = $this->request->post['tmdbirthday_coupons'];
		} else {
			$data['tmdbirthday_coupons'] = $this->config->get('tmdbirthday_coupons');
		}

		if (isset($this->request->post['tmdbirthday_start'])) {
			$data['tmdbirthday_start'] = $this->request->post['tmdbirthday_start'];
		} else {
			$data['tmdbirthday_start'] = $this->config->get('tmdbirthday_start');
		}
		
		if (isset($this->request->post['tmdbirthday_end'])) {
			$data['tmdbirthday_end'] = $this->request->post['tmdbirthday_end'];
		} else {
			$data['tmdbirthday_end'] = $this->config->get('tmdbirthday_end');
		}
		
		if (isset($this->request->post['tmdbirthday_dateformate'])) {
			$data['tmdbirthday_dateformate'] = $this->request->post['tmdbirthday_dateformate'];
		} else {
			$data['tmdbirthday_dateformate'] = $this->config->get('tmdbirthday_dateformate');
		}
		
		if (isset($this->request->post['tmdbirthday_dis_type'])) {
			$data['tmdbirthday_dis_type'] = $this->request->post['tmdbirthday_dis_type'];
		} else {
			$data['tmdbirthday_dis_type'] = $this->config->get('tmdbirthday_dis_type');
		}
		
		if (isset($this->request->post['tmdbirthday_dis_value'])) {
			$data['tmdbirthday_dis_value'] = $this->request->post['tmdbirthday_dis_value'];
		} else {
			$data['tmdbirthday_dis_value'] = $this->config->get('tmdbirthday_dis_value');
		}
		
		if (isset($this->request->post['tmdbirthday_total_amount'])) {
			$data['tmdbirthday_total_amount'] = $this->request->post['tmdbirthday_total_amount'];
		} else {
			$data['tmdbirthday_total_amount'] = $this->config->get('tmdbirthday_total_amount');
		}

		if (isset($this->request->post['tmdbirthday_use_coupon'])) {
			$data['tmdbirthday_use_coupon'] = $this->request->post['tmdbirthday_use_coupon'];
		} else {
			$data['tmdbirthday_use_coupon'] = $this->config->get('tmdbirthday_use_coupon');
		}

		if (isset($this->request->post['tmdbirthday_use_customer'])) {
			$data['tmdbirthday_use_customer'] = $this->request->post['tmdbirthday_use_customer'];
		} else {
			$data['tmdbirthday_use_customer'] = $this->config->get('tmdbirthday_use_customer');
		}

		if (isset($this->request->post['tmdbirthday_subject'])) {
			$data['tmdbirthday_subject'] = $this->request->post['tmdbirthday_subject'];
		} else {
			$data['tmdbirthday_subject'] = $this->config->get('tmdbirthday_subject');
		}

		if (isset($this->request->post['tmdbirthday_message'])) {
			$data['tmdbirthday_message'] = $this->request->post['tmdbirthday_message'];
		} else {
			$data['tmdbirthday_message'] = $this->config->get('tmdbirthday_message');
		}
		
		if (isset($this->request->post['tmdbirthday_cronjobstatus'])) {
			$data['tmdbirthday_cronjobstatus'] = $this->request->post['tmdbirthday_cronjobstatus'];
		} else {
			$data['tmdbirthday_cronjobstatus'] = $this->config->get('tmdbirthday_cronjobstatus');
		}
		
		$data['cronjobstatus'] = HTTP_CATALOG.'index.php?route=extension/tmdcronjob';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['customers'] = array();

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'	=> $this->config->get('config_limit_admin')
		);

		$customer_total = $this->model_extension_tmdbirthday->getTotalCustomers($filter_data);

		$results = $this->model_extension_tmdbirthday->getCustomers($filter_data);

		foreach ($results as $result) {
			$data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'date_of_birth'  => $result['date_of_birth'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'customer_group' => $result['customer_group'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'           => $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, true),
				'href' 			=> $this->url->link('extension/module/tmdbirthday/managecustomer', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, true),
			);
		}
		
		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/tmdbirthday', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['token']             = $this->session->data['token'];
		
		$data['tmdcalender'] = $this->load->controller('extension/tmdcalender');
		$data['tmdcronjob'] = $this->load->controller('extension/tmdcronjob');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tmdbirthday', $data));
	}
	
	public function managecustomer() {
		$this->load->language('extension/module/tmdbirthday');
		$this->load->model('extension/tmdbirthday');
			
		$data['text_list']           	= $this->language->get('text_list');
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		$data['text_confirm']			= $this->language->get('text_confirm');
		$data['text_none'] 				= $this->language->get('text_none');
		$data['text_select']            = $this->language->get('text_select');
		$data['text_loading']            = $this->language->get('text_loading');
		$data['text_reminder']            = $this->language->get('text_reminder');
		$data['text_addgift']            = $this->language->get('text_addgift');
		
		$data['entry_to_mail']            = $this->language->get('entry_to_mail');
		$data['entry_dob']            = $this->language->get('entry_dob');
		$data['entry_code']            = $this->language->get('entry_code');
		$data['entry_date_start']            = $this->language->get('entry_date_start');
		$data['entry_date_end']            = $this->language->get('entry_date_end');
		$data['entry_dis_type']            = $this->language->get('entry_dis_type');
		$data['entry_dis_value']            = $this->language->get('entry_dis_value');
		$data['entry_total']            = $this->language->get('entry_total');
		$data['entry_use_coupon']            = $this->language->get('entry_use_coupon');
		$data['entry_use_customer']            = $this->language->get('entry_use_customer');
		$data['entry_subject']            = $this->language->get('entry_subject');
		$data['entry_message']            = $this->language->get('entry_message');
		
		$data['button_save']          	= $this->language->get('button_save');
		$data['button_cancel']          = $this->language->get('button_cancel');

		$data['token']             = $this->session->data['token'];
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		if(isset($this->request->get['customer_id'])){
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}
		
		$data['types']= array();
		$data['types'][] = array(
			'text'  => $this->language->get('text_percent'),
			'value' => 'P'
		);
		$data['types'][] = array(
			'text'  => $this->language->get('text_fixe'),
			'value' => 'F'
		);
		
		$this->load->model('customer/customer');
		$customer_info = $this->model_customer_customer->getCustomer($customer_id);
		if(isset($customer_info['email'])) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}
		
		if(isset($customer_info['date_of_birth'])) {
			$data['date_of_birth'] = $customer_info['date_of_birth'];
		} else {
			$data['date_of_birth'] = '';
		}
		
		if(isset($customer_info['firstname'])) {
			$data['name'] = $customer_info['firstname'].' '.$customer_info['lastname'];
		} else {
			$data['name'] = '';
		}
		
		if(isset($this->request->get['customer_id'])){
			$data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}
		
		$data['code'] = rand(000000, 999999);
		$end = $this->config->get('tmdbirthday_end');
		
		$data['tmdbirthday_subject']	=	$this->config->get('tmdbirthday_subject');
		$data['tmdbirthday_message']	=	$this->config->get('tmdbirthday_message');

		$data['date_start'] = $this->config->get('tmdbirthday_start');
		$data['date_end'] = $this->config->get('tmdbirthday_end');
		
		$data['tmdbirthday_dis_value'] = '';
		$data['tmdbirthday_total_amount'] = '';
		
		$find = array(
			'{firstname}',
			'{lastname}',
			'{coupon_code}',
		);
		$replace = array(
			'firstname' => $customer_info['firstname'],
			'lastname' => $customer_info['lastname'],
			'coupon_code' => $data['code'],
		);
				
		$data['messages'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $data['tmdbirthday_message']))));
		
		$url='';

		$data['action1'] = $this->url->link('extension/module/tmdbirthday', 'token=' . $this->session->data['token'] .'&customer_id=' . $this->request->get['customer_id'] .$url, true);			
		
		$this->response->setOutput($this->load->view('extension/tmdcustomer', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tmdbirthday')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function addgift() {
        $json = array();
        $this->load->model('extension/tmdbirthday');
        $this->load->language('extension/module/tmdbirthday');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

        	if(isset($this->request->post['date_start'])) {
				$date_start = $this->request->post['date_start'];
			} else {
				$date_start ='';
			}

			if(isset($this->request->post['date_end'])) {
				$date_end = $this->request->post['date_end'];
			} else {
				$date_end ='';
			}

			if(empty($this->request->post['date_start'])) {
			  $json['error']['error_date_start'] = $this->language->get('error_date_start');
			}
			if(empty($this->request->post['date_end'])) {
			  $json['error']['error_date_end'] = $this->language->get('error_date_end');
			}
			
			if(empty($this->request->post['dis_value'])) {
				$json['error']['error_dis_val'] = $this->language->get('error_dis_value');
			}
			
			if(empty($this->request->post['total_amount'])) {
				$json['error']['error_total'] = $this->language->get('error_total');
			} else {
				$exit_info = $this->model_extension_tmdbirthday->getExitsCustomerCoupon($this->request->get['customer_id']);
				if(isset($exit_info['coupon_id'])) {
					$coupon_id = $exit_info['coupon_id'];
				} else {
					$coupon_id =0;
				}
				$coupon_info = $this->model_extension_tmdbirthday->getExitsCoupon($coupon_id,$date_start,$date_end);
				if(!empty($coupon_info)) {
					$json['error']['error_coupon_exit'] = $this->language->get('error_coupon_exit');
					$json['error']['error_start_date'] = $this->language->get('error_start_date');
					$json['error']['error_end_date'] = $this->language->get('error_end_date');
				}
			}
			if(!$json) {
				$this->model_extension_tmdbirthday->addTmdCoupon($this->request->post,$this->request->get['customer_id']);
				$json['success'] = $this->language->get('text_success');
			}
        }                   
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function allcustomer() {
        $json = array();
        $json['customers']=array();
        $this->load->model('extension/tmdbirthday');
        $this->load->language('extension/module/tmdbirthday');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

        	if(isset($this->request->get['date_of_birth'])) {
        		$dates = $this->request->get['date_of_birth'];
        	} else {
        		$dates ='';
        	}

        	$date_of_birth = date('Y-m-d',strtotime($dates));

			$results = $this->model_extension_tmdbirthday->getDateByCustomers($date_of_birth);

			foreach ($results as $result) {
				$apply = $this->model_extension_tmdbirthday->getApplyCoupon($result['customer_id']);
				if($apply!=0) {
					$apply = '<span style="color:#09b118; font-weight:bold;"><i class="fa fa-check-square-o"></i> '.$apply.' Coupon </span>';
				} else {
					$apply = '<span style="color:#e00718; font-weight:bold;"><i class="fa fa-times"></i> '.$apply.' Coupon </span>';
				}
				$json['customers'][] = array(
					'customer_id'    => $result['customer_id'],
					'apply'  		 => $apply,
					'date_of_birth'  => $result['date_of_birth'],
					'name'           => $result['name'],
					'email'          => $result['email'],
					'customer_group' => $result['customer_group'],
					'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'edit'           => $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id']),
					'href' 			=> $this->url->link('extension/module/tmdbirthday/managecustomer', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id']),
				);
			}	
        }                   
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function generateCoupon() {
        $json = array();
        $this->load->model('extension/tmdbirthday');
        $this->load->language('extension/module/tmdbirthday');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
			$json['code'] = rand(000000, 999999);		
            $json['success'] = $this->language->get('text_success');
                    
        }                   
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}