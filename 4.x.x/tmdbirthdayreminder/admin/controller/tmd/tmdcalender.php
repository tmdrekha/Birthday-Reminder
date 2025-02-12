<?php
namespace Opencart\Admin\Controller\Extension\tmdbirthdayreminder\Tmd;
class Tmdcalender extends \Opencart\System\Engine\Controller {
	public function index() {

		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		
		if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), VERSION);
		} else {
			$data['text_version'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');

		$data['VERSION']= VERSION;

		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');

		$data['tmdbirthday']=array();
		$data['cname']=array();
		$customers = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->getCustomers($data);
		$data['date'] = date('m/d/Y');

		$dm = date('y-m-d');
		
		foreach ($customers as $result) {
			if(isset($result['date_of_birth'])) {
				$dm1 = date('d-m',strtotime($result['date_of_birth']));
			} else {
				$n_dm ='';
			}
			
			$data['tmdbirthday'][]= "'".date('j-n',strtotime($result['date_of_birth']))."'";
		}
		
		if(!empty($data['tmdbirthday'])) {
			$activedates = implode(',',$data['tmdbirthday']);
		}else{
			$activedates ='';
		}
		$data['newdate']= implode(',', $data['tmdbirthday']);

		$data['date'] = date('d-m-Y');
		
		return $this->load->view('extension/tmdbirthdayreminder/tmd/tmdcalender', $data);
	}

	public function customereventdate(string&$route, array&$args, mixed&$output):void {

		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		$this->load->language('customer/customer');

		if (isset($this->request->get['customer_id'])) {
			$this->load->model('customer/customer');

			$customer_info = $this->model_customer_customer->getCustomer((int)$this->request->get['customer_id']);
		}

		if (!empty($customer_info)) {
			$args['date_of_birth'] = ($customer_info['date_of_birth'] != '0000-00-00') ? $customer_info['date_of_birth'] : '';
		} else {
			$args['date_of_birth'] = date('Y-m-d');
		}

		$template_buffer = $this->getTemplateBuffer($route, $output);
		$find            = '<div class="row mb-3{% if config_telephone_required %} required{% endif %}">';
		$replace         = '<div class="row mb-3">
                  <label for="input-birthday" class="col-sm-2 col-form-label">{{ entry_birthday }}</label>
                  <div class="col-sm-10 col-md-4">
                    <div class="input-group">
                      <input type="text" name="date_of_birth" value="{{ date_of_birth }}" placeholder="{{ entry_birthday }}" id="input-birthday" class="form-control date"/>
                      <div class="input-group-text"><i class="fa-regular fa-calendar"></i></div>
                      
                    </div>
                     <div id="error-birthday" class="invalid-feedback"></div>
                  </div>
                </div>'.'<div class="row mb-3{% if config_telephone_required %} required{% endif %}">';
		$output = str_replace($find, $replace, $template_buffer);

	}

	public function addCustomer(string&$route, array&$args, mixed $output):void {
	    $customers = '';

	      foreach ($args as $values) {
	          $customers = $values;
	      }


	    $this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
	    $product_publics = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->addCustomer($customers);

    }

  	public function editCustomer(string&$route, array&$args, mixed $output):void {

	    $customers = '';

	      foreach ($args as $values) {
	          $customers = $values;
	      }

	    $this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
	    $product_publics = $this->model_extension_tmdbirthdayreminder_tmd_tmdbirthday->editCustomer($customers);

  	}

	protected function getTemplateBuffer($route, $event_template_buffer) {

		// if there already is a modified template from view/*/before events use that one
		if ($event_template_buffer) {
			return $event_template_buffer;
		}

		// load the template file (possibly modified by ocmod and vqmod) into a string buffer

		if ($this->config->get('config_theme') == 'default') {
			$theme = $this->config->get('theme_default_directory');
		} else {
			$theme = $this->config->get('config_theme');
		}
		$dir_template = DIR_TEMPLATE;

		$template_file = $dir_template.$route.'.twig';
		if (file_exists($template_file) && is_file($template_file)) {

			return file_get_contents($template_file);
		}
		if ($this->isAdmin()) {
			trigger_error("Cannot find template file for route '$route'");
			exit;
		}
		$dir_template  = DIR_TEMPLATE.'default/template/';
		$template_file = $dir_template.$route.'.twig';
		if (file_exists($template_file) && is_file($template_file)) {

			return file_get_contents($template_file);
		}
		trigger_error("Cannot find template file for route '$route'");
		exit;
	}
}
