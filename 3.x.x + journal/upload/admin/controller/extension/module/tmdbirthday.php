<?php
//lib
require_once(DIR_SYSTEM.'library/tmd/system.php');
//lib
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
		$this->registry->set('tmd', new TMD($this->registry));
		$keydata=array(
		'code'=>'tmdkey_tmdbirthday',
		'eid'=>'NDE2NDI=',
		'route'=>'extension/module/tmdbirthday',
		);
		$tmdbirthday=$this->tmd->getkey($keydata['code']);
		$data['getkeyform']=$this->tmd->loadkeyform($keydata);
		
		$this->load->language('extension/module/tmdbirthday');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('extension/tmdbirthday');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_tmdbirthday', $this->request->post);

			$this->load->model('extension/tmdbirthday');
			$this->model_extension_tmdbirthday->addDefaultCoupon($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (!empty($this->request->get['status'])==1) {
				$this->response->redirect($this->url->link('extension/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
		
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'], true)
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

		$data['action'] = $this->url->link('extension/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'], true);
		$data['staysave'] = $this->url->link('extension/module/tmdbirthday', '&status=1&user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['module_status'] = $this->config->get('module_tmdbirthday_status');

		if (isset($this->request->post['module_tmdbirthday_status'])) {
			$data['module_tmdbirthday_status'] = $this->request->post['module_tmdbirthday_status'];
		} else {
			$data['module_tmdbirthday_status'] = $this->config->get('module_tmdbirthday_status');
		}
		
		if (isset($this->request->post['module_tmdbirthday_coupons'])) {
			$data['module_tmdbirthday_coupons'] = $this->request->post['module_tmdbirthday_coupons'];
		} else {
			$data['module_tmdbirthday_coupons'] = $this->config->get('module_tmdbirthday_coupons');
		}

		if (isset($this->request->post['module_tmdbirthday_start'])) {
			$data['module_tmdbirthday_start'] = $this->request->post['module_tmdbirthday_start'];
		} else {
			$data['module_tmdbirthday_start'] = $this->config->get('module_tmdbirthday_start');
		}
		
		if (isset($this->request->post['module_tmdbirthday_end'])) {
			$data['module_tmdbirthday_end'] = $this->request->post['module_tmdbirthday_end'];
		} else {
			$data['module_tmdbirthday_end'] = $this->config->get('module_tmdbirthday_end');
		}
		
		if (isset($this->request->post['module_tmdbirthday_dateformate'])) {
			$data['module_tmdbirthday_dateformate'] = $this->request->post['module_tmdbirthday_dateformate'];
		} else {
			$data['module_tmdbirthday_dateformate'] = $this->config->get('module_tmdbirthday_dateformate');
		}
		
		if (isset($this->request->post['module_tmdbirthday_dis_type'])) {
			$data['module_tmdbirthday_dis_type'] = $this->request->post['module_tmdbirthday_dis_type'];
		} else {
			$data['module_tmdbirthday_dis_type'] = $this->config->get('module_tmdbirthday_dis_type');
		}
		
		if (isset($this->request->post['module_tmdbirthday_dis_value'])) {
			$data['module_tmdbirthday_dis_value'] = $this->request->post['module_tmdbirthday_dis_value'];
		} else {
			$data['module_tmdbirthday_dis_value'] = $this->config->get('module_tmdbirthday_dis_value');
		}
		
		if (isset($this->request->post['module_tmdbirthday_total_amount'])) {
			$data['module_tmdbirthday_total_amount'] = $this->request->post['module_tmdbirthday_total_amount'];
		} else {
			$data['module_tmdbirthday_total_amount'] = $this->config->get('module_tmdbirthday_total_amount');
		}

		if (isset($this->request->post['module_tmdbirthday_use_coupon'])) {
			$data['module_tmdbirthday_use_coupon'] = $this->request->post['module_tmdbirthday_use_coupon'];
		} else {
			$data['module_tmdbirthday_use_coupon'] = $this->config->get('module_tmdbirthday_use_coupon');
		}

		if (isset($this->request->post['module_tmdbirthday_use_customer'])) {
			$data['module_tmdbirthday_use_customer'] = $this->request->post['module_tmdbirthday_use_customer'];
		} else {
			$data['module_tmdbirthday_use_customer'] = $this->config->get('module_tmdbirthday_use_customer');
		}

		if (isset($this->request->post['module_tmdbirthday_language'])) {
			$data['module_tmdbirthday_language'] = $this->request->post['module_tmdbirthday_language'];
		} else {
			$data['module_tmdbirthday_language'] = $this->config->get('module_tmdbirthday_language');
		}
		
		if (isset($this->request->post['module_tmdbirthday_cronjobstatus'])) {
			$data['module_tmdbirthday_cronjobstatus'] = $this->request->post['module_tmdbirthday_cronjobstatus'];
		} else {
			$data['module_tmdbirthday_cronjobstatus'] = $this->config->get('module_tmdbirthday_cronjobstatus');
		}

		if (isset($this->request->post['module_tmdbirthday_sendemaildays'])) {
			$data['module_tmdbirthday_sendemaildays'] = $this->request->post['module_tmdbirthday_sendemaildays'];
		} else {
			$data['module_tmdbirthday_sendemaildays'] = $this->config->get('module_tmdbirthday_sendemaildays');
		}

		// Products
		$this->load->model('catalog/product');
		if (isset($this->request->post['module_tmdbirthday_productids'])) {
			$productids = $this->request->post['module_tmdbirthday_productids'];
		} elseif ($this->config->get('module_tmdbirthday_productids')) {
			$productids = $this->config->get('module_tmdbirthday_productids');
		} else {
			$productids = array();
		}

		$data['tmdbirthday_productids'] = array();

		foreach ($productids as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);
			if ($related_info) {
				$data['tmdbirthday_productids'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['module_tmdbirthday_categoryids'])) {
			$categoryids = $this->request->post['module_tmdbirthday_categoryids'];
		} elseif ($this->config->get('module_tmdbirthday_categoryids')) {
			$categoryids = $this->config->get('module_tmdbirthday_categoryids');
		} else {
			$categoryids = array();
		}

		$data['tmdbirthday_categoryids'] = array();

		foreach ($categoryids as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);
			if ($category_info) {
				$data['tmdbirthday_categoryids'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
		
		$data['cronjobstatus'] = HTTP_CATALOG.'index.php?route=extension/tmdcronjob';
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['tmdcalender'] = $this->load->controller('extension/tmdcalender');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tmdbirthday', $data));
	}
	
	public function managecustomer() {
		$this->load->language('extension/module/tmdbirthday');
		$this->load->model('extension/tmdbirthday');
			
		$data['user_token'] = $this->session->data['user_token'];
		
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
		
		$data['code'] = 'DOB'.rand(00000000, 99999999);
		$end = $this->config->get('module_tmdbirthday_end');
		
		$module_language = $this->config->get('module_tmdbirthday_language');
		
		$data['tmdbirthday_subject'] = '';
		if(!empty($module_language[$this->config->get('config_language_id')]['subject'])) {
			$data['tmdbirthday_subject'] = $module_language[$this->config->get('config_language_id')]['subject'];
		}

		$data['tmdbirthday_message'] = '';
		if(!empty($module_language[$this->config->get('config_language_id')]['message'])) {
			$data['tmdbirthday_message'] = html_entity_decode($module_language[$this->config->get('config_language_id')]['message']);
		}

		$data['date_start'] = $this->config->get('module_tmdbirthday_start');
		$data['date_end'] 	= $this->config->get('module_tmdbirthday_end');
		
		$find = array(
			'{customer}',
			'{coupon_code}',
			'{end_date}',
			'{store_name}',
		);
		$replace = array(
			'customer' 		=> $customer_info['firstname'].' '.$customer_info['lastname'],
			'coupon_code' 	=> $data['code'],
			'end_date' 		=> $this->config->get('module_tmdbirthday_end'),
			'store_name' 	=> $this->config->get('config_name'),
		);
		
		$data['messages'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $data['tmdbirthday_message']))));
		
		$data['action1'] = $this->url->link('extension/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'] .'&customer_id=' . $this->request->get['customer_id']);			
		
		$this->response->setOutput($this->load->view('extension/tmdcustomer', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tmdbirthday')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		$tmdbirthday=$this->config->get('tmdkey_tmdbirthday');
		if (empty(trim($tmdbirthday))) {			
		$this->session->data['warning'] ='Module will Work after add License key!';
		$this->response->redirect($this->url->link('extension/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		return !$this->error;
	}
	
	public function keysubmit() {
		$json = array(); 
		
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$keydata=array(
			'code'=>'tmdkey_tmdbirthday',
			'eid'=>'NDE2NDI=',
			'route'=>'extension/module/tmdbirthday',
			'moduledata_key'=>$this->request->post['moduledata_key'],
			);
			$this->registry->set('tmd', new TMD($this->registry));
            $json=$this->tmd->matchkey($keydata);       
		} 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
					$json['error']['error_start_date'] 	= $this->language->get('error_start_date');
					$json['error']['error_end_date'] 	= $this->language->get('error_end_date');
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
					'edit'           => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
					'href' 			 => $this->url->link('extension/module/tmdbirthday/managecustomer', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
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
			$json['code'] = 'DOB'.rand(00000000, 99999999);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}