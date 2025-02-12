<?php
class ControllerExtensionTmdcalender extends Controller {
	public function index() {
		if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), VERSION);
		} else {
			$data['text_version'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];
		$this->load->model('extension/tmdbirthday');

		$data['tmdbirthday']=array();
		$data['cname']=array();
		$customers = $this->model_extension_tmdbirthday->getCustomers($data);
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
		
		return $this->load->view('extension/tmdcalender', $data);
	}
}