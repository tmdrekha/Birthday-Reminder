<?php
namespace Opencart\Admin\Controller\Extension\tmdbirthdayreminder\Module;
// Lib Include 
require_once(DIR_EXTENSION.'/tmdbirthdayreminder/system/library/tmd/system.php');
// Lib Include 
class Tmdbirthday extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		$this->registry->set('tmd', new  \Tmdbirthdayreminder\System\Library\Tmd\System($this->registry));
		$keydata=array(
		'code'=>'tmdkey_tmdbirthday',
		'eid'=>'NDE2NDI=',
		'route'=>'extension/tmdbirthdayreminder/module/tmdbirthday',
		);
		$tmdbirthday=$this->tmd->getkey($keydata['code']);
		$data['getkeyform']=$this->tmd->loadkeyform($keydata);
		
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		$data['VERSION'] = VERSION;

		$this->document->setTitle($this->language->get('heading_title1'));
		
		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
		
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday', 'user_token=' . $this->session->data['user_token'])
		];

		$data['VERSION']= VERSION;

		$data['dateformates']= [];
		$data['dateformates'][] = [
			'text'  => $this->language->get('text_f1'),
			'value' => 'YYYY-MM-DD'
		];
		$data['dateformates'][] = [
			'text'  => $this->language->get('text_f2'),
			'value' => 'MM-DD-YYYY'
		];
		$data['dateformates'][] = [
			'text'  => $this->language->get('text_f3'),
			'value' => 'DD-MM-YYYY'
		];
		
		$data['types']= [];
		$data['types'][] = [
			'text'  => $this->language->get('text_percent'),
			'value' => 'P'
		];
		$data['types'][] = [
			'text'  => $this->language->get('text_fixe'),
			'value' => 'F'
		];
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');

		if(VERSION>='4.0.2.0'){
			$data['save'] = $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday.save', 'user_token=' . $this->session->data['user_token']);
		}else{
			$data['save'] = $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday|save', 'user_token=' . $this->session->data['user_token']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_status'] = $this->config->get('module_tmdbirthday_status');
		$data['module_tmdbirthday_status'] = $this->config->get('module_tmdbirthday_status');
		$data['module_tmdbirthday_coupons'] = $this->config->get('module_tmdbirthday_coupons');
		
		$data['module_tmdbirthday_start'] = $this->config->get('module_tmdbirthday_start');
		$data['module_tmdbirthday_end'] = $this->config->get('module_tmdbirthday_end');

		$data['module_tmdbirthday_dateformate'] = $this->config->get('module_tmdbirthday_dateformate');
		$data['module_tmdbirthday_dis_type'] = $this->config->get('module_tmdbirthday_dis_type');
		$data['module_tmdbirthday_dis_value'] = $this->config->get('module_tmdbirthday_dis_value');
		$data['module_tmdbirthday_total_amount'] = $this->config->get('module_tmdbirthday_total_amount');
		$data['module_tmdbirthday_use_coupon'] = $this->config->get('module_tmdbirthday_use_coupon');
		$data['module_tmdbirthday_use_customer'] = $this->config->get('module_tmdbirthday_use_customer');
		$data['module_tmdbirthday_subject'] = $this->config->get('module_tmdbirthday_subject');
		$data['module_tmdbirthday_message'] = $this->config->get('module_tmdbirthday_message');
		$data['module_tmdbirthday_cronjobstatus'] = $this->config->get('module_tmdbirthday_cronjobstatus');
		$data['module_tmdbirthday_language'] = $this->config->get('module_tmdbirthday_language');

		if (!empty($this->config->get('module_tmdbirthday_sendemaildays'))) {
			$data['module_tmdbirthday_sendemaildays'] = $this->config->get('module_tmdbirthday_sendemaildays');
		}else{
			$data['module_tmdbirthday_sendemaildays'] = 0;			
		}

		// Products
		$this->load->model('catalog/product');
		if ($this->config->get('module_tmdbirthday_productids')) {
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
		if ($this->config->get('module_tmdbirthday_categoryids')) {
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

		$data['cronjobstatus'] = HTTP_CATALOG.'index.php?route=extension/tmdbirthdayreminder/tmd/tmdcronjob';
				
		$data['user_token'] = $this->session->data['user_token'];

		$data['tmdcalender'] = $this->load->controller('extension/tmdbirthdayreminder/tmd/tmdcalender');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/tmdbirthdayreminder/module/tmdbirthday', $data));
	}

	public function save(): void {
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/tmdbirthdayreminder/module/tmdbirthday')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		$tmdbirthday=$this->config->get('tmdkey_tmdbirthday');
		if (empty(trim($tmdbirthday))) {			
		$json['error'] ='Module will Work after add License key!';
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_tmdbirthday', $this->request->post);

			$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
			$this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->addDefaultCoupon($this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function keysubmit() {
		$json = array(); 
		
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$keydata=array(
			'code'=>'tmdkey_tmdbirthday',
			'eid'=>'NDE2NDI=',
			'route'=>'extension/tmdbirthdayreminder/module/tmdbirthday',
			'moduledata_key'=>$this->request->post['moduledata_key'],
			);
			$this->registry->set('tmd', new  \Tmdbirthdayreminder\System\Library\Tmd\System($this->registry));
		
            $json=$this->tmd->matchkey($keydata);       
		} 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function install() {
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		$this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->install();

		// TMD GST Dashboard events
		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/module/tmdbirthday.menu';
		}
		else{
			$eventaction='extension/tmdbirthdayreminder/module/tmdbirthday|menu';
		}
		
		$this->model_setting_event->deleteEventByCode('tmd_birthdaymenu');
		
		$eventrequest=[
			'code'=>'tmd_birthdaymenu',
			'description'=>'TMD Birthday Reminder Menu',
			'trigger'=>'admin/view/common/column_left/before',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0'){	
			$this->model_setting_event->addEvent('tmd_birthdaymenu', 'TMD Birthday Reminder Menu', 'admin/view/common/column_left/before','extension/tmdbirthdayreminder/module/tmdbirthday|menu', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Admin customer form
		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.customereventdate';
		}else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|customereventdate';
		}

		$this->model_setting_event->deleteEventByCode('tmd_customername');
		$eventrequest=[
			'code'=>'tmd_customername',
			'description'=>'TMD Customer field',
			'trigger'=>'admin/view/customer/customer_form/before',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0'){
			$this->model_setting_event->addEvent('tmd_customername', 'TMD Customer field', 'admin/view/customer/customer_form/before','extension/tmdbirthdayreminder/tmd/tmdcalender|customereventdate', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Admin Add Customer 
		$this->model_setting_event->deleteEventByCode('tmd_addCustomer');
		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.addCustomer';
		}else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|addCustomer';
		}

		$eventrequest=[
			'code'=>'tmd_addCustomer',
			'description'=>'TMD Add Customer',
			'trigger'=>'admin/model/customer/customer/addCustomer/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->addEvent('tmd_addCustomer', 'TMD Add Customer', 'admin/model/customer/customer/addCustomer/after','extension/tmdbirthdayreminder/tmd/tmdcalender|addCustomer', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}
		
		// Admin Edit Customer 
		$this->model_setting_event->deleteEventByCode('tmd_editCustomer');
		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.editCustomer';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|editCustomer';
		}
		$eventrequest=[
			'code'=>'tmd_editCustomer',
			'description'=>'TMD Edit Customer',
			'trigger'=>'admin/model/customer/customer/editCustomer/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->addEvent('tmd_editCustomer', 'TMD Edit Customer', 'admin/model/customer/customer/editCustomer/after','extension/tmdbirthdayreminder/tmd/tmdcalender|editCustomer', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front Get Checkout register
		$this->model_setting_event->deleteEventByCode('tmd_accountregisterform');
		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.accountregisterform';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|accountregisterform';
		}		
		$eventrequest=[
			'code'=>'tmd_accountregisterform',
			'description'=>'TMD Checkout Register Form',
			'trigger'=>'catalog/view/account/register/before',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->addEvent('tmd_accountregisterform', 'TMD Checkout Register Form', 'catalog/view/account/register/before','extension/tmdbirthdayreminder/tmd/tmdcalender|accountregisterform', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// 
		// Front Get Checkout register
		$this->model_setting_event->deleteEventByCode('tmd_checkoutregisterform');
		if(VERSION>='4.0.2.0') {
            $eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.checkoutregisterform';
        } else{
            $eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|checkoutregisterform';
        }

		$eventrequest=[
			'code'=>'tmd_checkoutregisterform',
			'description'=>'TMD Checkout Register Form',
			'trigger'=>'catalog/view/checkout/register/before',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->addEvent('tmd_checkoutregisterform', 'TMD Checkout Register Form', 'catalog/view/checkout/register/before','extension/tmdbirthdayreminder/tmd/tmdcalender|checkoutregisterform', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front Add Customer 
		$this->model_setting_event->deleteEventByCode('tmd_addFrontCustomer');

		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.addCustomer';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|addCustomer';
		}
		$eventrequest=[
			'code'=>'tmd_addFrontCustomer',
			'description'=>'TMD Front Add Customer',
			'trigger'=>'catalog/model/account/customer/addCustomer/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->addEvent('tmd_addFrontCustomer', 'TMD Front Add Customer', 'catalog/model/account/customer/addCustomer/after','extension/tmdbirthdayreminder/tmd/tmdcalender|addCustomer', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front Edit Customer 
		$this->model_setting_event->deleteEventByCode('tmd_editFrontCustomer');

		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.editCustomer';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|editCustomer';
		}
		$eventrequest=[
			'code'=>'tmd_editFrontCustomer',
			'description'=>'TMD Front Edit Customer',
			'trigger'=>'catalog/model/account/customer/editCustomer/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->editEvent('tmd_editFrontCustomer', 'TMD Front Edit Customer', 'catalog/model/account/customer/editCustomer/after','extension/tmdbirthdayreminder/tmd/tmdcalender|editCustomer', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front GetCoupon 
		$this->model_setting_event->deleteEventByCode('tmd_getCoupon');

		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.getCoupon';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|getCoupon';
		}
		$eventrequest=[
			'code'=>'tmd_getCoupon',
			'description'=>'TMD Front GetCoupon',
			'trigger'=>'catalog/model/marketing/coupon/getCoupon/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->editEvent('tmd_getCoupon', 'TMD Front GetCoupon', 'catalog/model/marketing/coupon/getCoupon/after','extension/tmdbirthdayreminder/tmd/tmdcalender|getCoupon', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front GetCoupon 
		$this->model_setting_event->deleteEventByCode('tmd_getTotal');

		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.getTotal';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|getTotal';
		}

		$eventrequest=[
			'code'=>'tmd_getTotal',
			'description'=>'TMD Front GetTotal',
			'trigger'=>'catalog/model/extension/opencart/total/coupon/getTotal/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->editEvent('tmd_getTotal', 'TMD Front GetTotal', 'catalog/model/extension/opencart/total/coupon/getTotal/after','extension/tmdbirthdayreminder/tmd/tmdcalender|getTotal', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Front Confirm 
		$this->model_setting_event->deleteEventByCode('tmd_Confirm');

		if(VERSION>='4.0.2.0'){
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender.Confirm';
		} else{
			$eventaction='extension/tmdbirthdayreminder/tmd/tmdcalender|Confirm';
		}

		$eventrequest=[
			'code'=>'tmd_Confirm',
			'description'=>'TMD Front Confirm',
			'trigger'=>'catalog/controller/extension/opencart/total/coupon/confirm/after',
			'action'=>$eventaction,
			'status'=>'1',
			'sort_order'=>'1',
		];

		if(VERSION=='4.0.0.0') {
			$this->model_setting_event->editEvent('tmd_Confirm', 'TMD Front Confirm', 'catalog/controller/extension/opencart/total/coupon/confirm/after','extension/tmdbirthdayreminder/tmd/tmdcalender|Confirm', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		// Add Cron From Admin
		$this->load->model('setting/cron');
		$this->model_setting_cron->deleteCronByCode('tmd_birthdayremindercron');
	
		$this->model_setting_cron->addCron('tmd_birthdayremindercron','Cron job for birthdayreminder','day','extension/tmdbirthdayreminder/module/tmdbirthday','1');
		
		// Add startup to catalog
		$startup_data = [
			'code'        => 'storecart',
			'description' => 'storecart extension',
			'action'      => 'catalog/extension/tmdbirthdayreminder/startup/storecart',
			'status'      => 1,
			'sort_order'  => 2
		];

		// end
	}
	
	public function uninstall() {
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		$this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->uninstall();

		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('tmd_birthdaymenu');

		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('tmd_checkoutregisterform');

		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('tmd_addFrontCustomer');
	}	

	public function menu(string&$route, array&$args, mixed&$output):void {
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

		$tmdbirthday_status=$this->config->get('module_tmdbirthday_status');

		if(!empty($tmdbirthday_status)){
			$birthdayreminder = [];
			$args['menus'][] = [
				'id'       => 'menu-extension',
				'icon'	   => 'fa fa-birthday-cake', 
				'name'	   => $this->language->get('text_birtdayreminder'),
				'href'     => $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday', 'user_token=' . $this->session->data['user_token']),
				'children' => []
			];
		}	
	}

	public function managecustomer() {
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		$data['VERSION']= VERSION;
		$data['text_list']           	= $this->language->get('text_list');
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		$data['text_confirm']			= $this->language->get('text_confirm');
		$data['text_none'] 				= $this->language->get('text_none');
		$data['text_select']            = $this->language->get('text_select');
		$data['text_loading']           = $this->language->get('text_loading');
		
		$data['button_save']          	= $this->language->get('button_save');
		$data['button_cancel']          = $this->language->get('button_cancel');

		$data['user_token']             = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');


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

		$this->response->setOutput($this->load->view('extension/tmdbirthdayreminder/tmd/tmdcustomer', $data));
	}

	public function addgift() {
        $json = [];
        $this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
        $this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

        $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');
            $data['VERSION'] = VERSION;
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
				$exit_info = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getExitsCustomerCoupon($this->request->get['customer_id']);
				if(isset($exit_info['coupon_id'])) {
					$coupon_id = $exit_info['coupon_id'];
				} else {
					$coupon_id =0;
				}
				
				$coupon_info = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getExitsCoupon($coupon_id, $date_start, $date_end);
				
				if(!empty($coupon_info)) {
					$json['error']['error_coupon_exit'] = $this->language->get('error_coupon_exit');
					$json['error']['error_start_date'] 	= $this->language->get('error_start_date');
					$json['error']['error_end_date'] 	= $this->language->get('error_end_date');
				}
			}
			if(!$json) {
				$apply = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getApplyCoupon($this->request->get['customer_id']);

				if($apply!=0) {
					$json['apply']= '<span style="color:#09b118; font-weight:bold;"><i class="fa fa-check-square-o"></i> '.$apply.' Coupon </span>';
				} else {
					$json['apply'] = '<span style="color:#e00718; font-weight:bold;"><i class="fa fa-times"></i> '.$apply.' Coupon </span>';
				}

				$customer_id=$this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->addTmdCoupon($this->request->post,$this->request->get['customer_id']);
				$json['success'] = $this->language->get('text_success');
			}
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function allcustomer() {
        $json = [];
        $json['customers']=[];
        $this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
        $this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

        $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

        	if(isset($this->request->get['date_of_birth'])) {
        		$dates = $this->request->get['date_of_birth'];
        	} else {
        		$dates ='';
        	}

        	$date_of_birth = date('Y-m-d',strtotime($dates));

			$results = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getDateByCustomers($date_of_birth);

			foreach ($results as $result) {
				$apply = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getApplyCoupon($result['customer_id']);
				if($apply!=0) {
					$apply = '<span style="color:#09b118; font-weight:bold;"><i class="fa fa-check-square-o"></i> '.$apply.' Coupon </span>';
				} else {
					$apply = '<span style="color:#e00718; font-weight:bold;"><i class="fa fa-times"></i> '.$apply.' Coupon </span>';
				}

				if(VERSION>='4.0.2.0'){
					$json['customers'][] = [
						'customer_id'    => $result['customer_id'],
						'apply'  		 => $apply,
						'date_of_birth'  => $result['date_of_birth'],
						'name'           => $result['name'],
						'email'          => $result['email'],
						'customer_group' => $result['customer_group'],
						'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
						'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'edit'           => $this->url->link('customer/customer.form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
						'href' 			=> $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday.managecustomer', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
					];
				}else{
					$json['customers'][] = [
						'customer_id'    => $result['customer_id'],
						'apply'  		 => $apply,
						'date_of_birth'  => $result['date_of_birth'],
						'name'           => $result['name'],
						'email'          => $result['email'],
						'customer_group' => $result['customer_group'],
						'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
						'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'edit'           => $this->url->link('customer/customer|form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
						'href' 			=> $this->url->link('extension/tmdbirthdayreminder/module/tmdbirthday|managecustomer', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id']),
					];
				}
			}	
        }                   
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function generateCoupon() {
        $json = array();
        $this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
        $this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$json['code'] = 'DOB'.rand(00000000, 99999999);
            $json['success'] = $this->language->get('text_success');
        }                   
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}