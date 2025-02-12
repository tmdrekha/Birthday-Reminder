<?php
namespace Opencart\catalog\Controller\Extension\tmdbirthdayreminder\Tmd;
use \Opencart\System\Helper as Helper;
class Tmdcalender extends \Opencart\System\Engine\Controller {
	public function checkoutregisterform(string&$route, array&$args, mixed&$output):void {
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		$this->load->language('checkout/register');
		
		$args['tmdbirthday_status']	= $this->config->get('module_tmdbirthday_status');
		$tmdbirthday_status 		= $this->config->get('module_tmdbirthday_status');
		if($tmdbirthday_status) { 
			if (isset($this->request->post['date_of_birth'])) {
				$args['date_of_birth'] = $this->request->post['date_of_birth'];
			} else {
				$args['date_of_birth'] = '';
			}

			$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
			if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['date_of_birth'])) {
				$args['entry_birthday'] = $tmdbirthday_language[$this->config->get('config_language_id')]['date_of_birth'];
			} else {
				$args['entry_birthday'] = $this->language->get('entry_birthday');
			}

			if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['dob_necessarytext'])) {
				$args['text_correctdate'] = $tmdbirthday_language[$this->config->get('config_language_id')]['dob_necessarytext'];
			} else {
				$args['text_correctdate'] = $this->language->get('text_correctdate');
			}
		}
		
		if(VERSION>='4.0.2.0'){
			$template_buffer = $this->getTemplateBuffer($route, $output);
			$find            = 'checkout/register.save';		
			$replace         = 'extension/tmdbirthdayreminder/tmd/tmdcalender.save';
		}else{
			$template_buffer = $this->getTemplateBuffer($route, $output);
			$find            = 'checkout/register|save';		
			$replace         = 'extension/tmdbirthdayreminder/tmd/tmdcalender|savepage';
		}
		
		$output = str_replace($find, $replace, $template_buffer);

		$template_buffer = $this->getTemplateBuffer($route, $output);
		$find            = '{% if config_telephone_display %}';		
		$replace         = '{% if tmdbirthday_status %}<div class="col mb-3 required">
        <label for="input-email" class="form-label">{{ entry_birthday }}</label>
          <div class="input-group">
            <input type="text" name="date_of_birth" value="{{ date_of_birth }}" placeholder="{{ entry_birthday }}" id="input-birthday" class="form-control tmddate"/>
            <div class="input-group-text"><i class="fa-regular fa-calendar"></i></div>
              <div id="error-birthday" class="invalid-feedback"></div>
          </div>
          <div class="float-end">
            <i>{{ text_correctdate }}</i>
          </div>
        </div>{% endif %}'.'{% if config_telephone_display %}';
		
		$output = str_replace($find, $replace, $template_buffer);
	}

	public function savepage(): void {
		$this->load->language('checkout/register');

		$json = [];

		$keys = [
			'account',
			'customer_group_id',
			'firstname',
			'lastname',
			'email',
			'telephone',
			'payment_company',
			'payment_address_1',
			'payment_address_2',
			'payment_city',
			'payment_postcode',
			'payment_country_id',
			'payment_zone_id',
			'payment_custom_field',
			'address_match',
			'shipping_firstname',
			'shipping_lastname',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_postcode',
			'shipping_country_id',
			'shipping_zone_id',
			'shipping_custom_field',
			'password',
			'agree'
		];

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			if (!$product['minimum']) {
				$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);

				break;
			}
		}

		if (!$json) {
			// If not guest checkout disabled, login require price or cart has downloads
			if (!$this->request->post['account'] && (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload() || $this->cart->hasSubscription())) {
				$json['error']['warning'] = $this->language->get('error_guest');
			}

			// Customer Group
			if ($this->request->post['customer_group_id']) {
				$customer_group_id = (int)$this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if (!$customer_group_info || !in_array($customer_group_id, (array)$this->config->get('config_customer_group_display'))) {
				$json['error']['warning'] = $this->language->get('error_customer_group');
			}

			if ((Helper\Utf8\strlen($this->request->post['firstname']) < 1) || (Helper\Utf8\strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			$tmdbirthday_status 		= $this->config->get('module_tmdbirthday_status');
			if($tmdbirthday_status) { 
				$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
				if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'])) {
					$error_dateofbirth = $tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'];
				} else {
					$error_dateofbirth = $this->language->get('error_dateofbirth');
				}
				if ((Helper\Utf8\strlen($this->request->post['date_of_birth']) < 1) || (Helper\Utf8\strlen($this->request->post['date_of_birth']) > 32)) {
					$json['error']['birthday'] = $error_dateofbirth;
				}
			}


			if ((Helper\Utf8\strlen($this->request->post['lastname']) < 1) || (Helper\Utf8\strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((Helper\Utf8\strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			$this->load->model('account/customer');

			if ($this->request->post['account'] && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			// Logged
			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

				if ($customer_info['customer_id'] != $this->customer->getId()) {
					$json['error']['warning'] = $this->language->get('error_exists');
				}
			}

			if ($this->config->get('config_telephone_required') && (Helper\Utf8\strlen($this->request->post['telephone']) < 3) || (Helper\Utf8\strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
					}
				}
			}

			if ($this->config->get('config_checkout_address')) {
				if ((Helper\Utf8\strlen($this->request->post['payment_address_1']) < 3) || (Helper\Utf8\strlen($this->request->post['payment_address_1']) > 128)) {
					$json['error']['payment_address_1'] = $this->language->get('error_address_1');
				}

				if ((Helper\Utf8\strlen($this->request->post['payment_city']) < 2) || (Helper\Utf8\strlen($this->request->post['payment_city']) > 32)) {
					$json['error']['payment_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$payment_country_info = $this->model_localisation_country->getCountry((int)$this->request->post['payment_country_id']);

				if ($payment_country_info && $payment_country_info['postcode_required'] && (Helper\Utf8\strlen($this->request->post['payment_postcode']) < 2 || Helper\Utf8\strlen($this->request->post['payment_postcode']) > 10)) {
					$json['error']['payment_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['payment_country_id'] == '') {
					$json['error']['payment_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['payment_zone_id'] == '') {
					$json['error']['payment_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}

			if ($this->cart->hasShipping() && !$this->request->post['address_match']) {
				// If payment address not required we need to use the firstname and lastname from the account.
				if ($this->config->get('config_checkout_address')) {
					if ((Helper\Utf8\strlen($this->request->post['shipping_firstname']) < 1) || (Helper\Utf8\strlen($this->request->post['shipping_firstname']) > 32)) {
						$json['error']['shipping_firstname'] = $this->language->get('error_firstname');
					}

					if ((Helper\Utf8\strlen($this->request->post['shipping_lastname']) < 1) || (Helper\Utf8\strlen($this->request->post['shipping_lastname']) > 32)) {
						$json['error']['shipping_lastname'] = $this->language->get('error_lastname');
					}
				}

				if ((Helper\Utf8\strlen($this->request->post['shipping_address_1']) < 3) || (Helper\Utf8\strlen($this->request->post['shipping_address_1']) > 128)) {
					$json['error']['shipping_address_1'] = $this->language->get('error_address_1');
				}

				if ((Helper\Utf8\strlen($this->request->post['shipping_city']) < 2) || (Helper\Utf8\strlen($this->request->post['shipping_city']) > 128)) {
					$json['error']['shipping_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$shipping_country_info = $this->model_localisation_country->getCountry((int)$this->request->post['shipping_country_id']);

				if ($shipping_country_info && $shipping_country_info['postcode_required'] && (Helper\Utf8\strlen($this->request->post['shipping_postcode']) < 2 || Helper\Utf8\strlen($this->request->post['shipping_postcode']) > 10)) {
					$json['error']['shipping_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['shipping_country_id'] == '') {
					$json['error']['shipping_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['shipping_zone_id'] == '') {
					$json['error']['shipping_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}

			// If account register password required
			if ($this->request->post['account'] && (Helper\Utf8\strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (Helper\Utf8\strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['account']) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

				if ($information_info && !$this->request->post['agree']) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			// Captcha
			$this->load->model('setting/extension');

			if (!$this->customer->isLogged()) {
				$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

				if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
					$captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '|validate');

					if ($captcha) {
						$json['error']['captcha'] = $captcha;
					}
				}
			}
		}

		if (!$json) {
			// Add customer details into session
			$customer_data = [
				'customer_id'       => 0,
				'customer_group_id' => $customer_group_id,
				'firstname'         => $this->request->post['firstname'],
				'lastname'          => $this->request->post['lastname'],
				'email'             => $this->request->post['email'],
				'telephone'         => $this->request->post['telephone'],
				'custom_field'      => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : []
			];

			// Register
			if ($this->request->post['account']) {
				$customer_data['customer_id'] = $this->model_account_customer->addCustomer($this->request->post);
			}

			// Logged so edit customer details
			if ($this->customer->isLogged()) {
				$this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);
			}

			// Check if current customer group requires approval
			if (!$customer_group_info['approval']) {
				$this->session->data['customer'] = $customer_data;
			}

			$this->load->model('account/address');

			// Payment Address
			if ($this->config->get('config_checkout_address')) {
				if (isset($this->session->data['payment_address'])) {
					$address_id = $this->session->data['payment_address']['address_id'];
				} else {
					$address_id = 0;
				}

				if ($payment_country_info) {
					$country = $payment_country_info['name'];
					$iso_code_2 = $payment_country_info['iso_code_2'];
					$iso_code_3 = $payment_country_info['iso_code_3'];
					$address_format = $payment_country_info['address_format'];
				} else {
					$country = '';
					$iso_code_2 = '';
					$iso_code_3 = '';
					$address_format = '';
				}

				$this->load->model('localisation/zone');

				$zone_info = $this->model_localisation_zone->getZone($this->request->post['payment_zone_id']);

				if ($zone_info) {
					$zone = $zone_info['name'];
					$zone_code = $zone_info['code'];
				} else {
					$zone = '';
					$zone_code = '';
				}

				$payment_address_data = [
					'address_id'     => $address_id,
					'firstname'      => $this->request->post['firstname'],
					'lastname'       => $this->request->post['lastname'],
					'company'        => $this->request->post['payment_company'],
					'address_1'      => $this->request->post['payment_address_1'],
					'address_2'      => $this->request->post['payment_address_2'],
					'city'           => $this->request->post['payment_city'],
					'postcode'       => $this->request->post['payment_postcode'],
					'zone_id'        => $this->request->post['payment_zone_id'],
					'zone'           => $zone,
					'zone_code'      => $zone_code,
					'country_id'     => $this->request->post['payment_country_id'],
					'country'        => $country,
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format,
					'custom_field'   => isset($this->request->post['payment_custom_field']) ? $this->request->post['payment_custom_field'] : []
				];

				// Add
				if ($this->request->post['account']) {
					$payment_address_data['default'] = 1;

					$payment_address_data['address_id'] = $this->model_account_address->addAddress($customer_data['customer_id'], $payment_address_data);
				}

				// Edit
				if ($this->customer->isLogged() && $payment_address_data['address_id']) {
					$this->model_account_address->editAddress($payment_address_data['address_id'], $payment_address_data);
				}

				// Requires Approval
				if (!$customer_group_info['approval']) {
					$this->session->data['payment_address'] = $payment_address_data;
				}
			}

			// Shipping Address
			if ($this->cart->hasShipping()) {
				if (!$this->request->post['address_match']) {
					if (isset($this->session->data['shipping_address'])) {
						$address_id = $this->session->data['shipping_address']['address_id'];
					} else {
						$address_id = 0;
					}

					if (!$this->config->get('config_checkout_address')) {
						$firstname = $this->request->post['firstname'];
						$lastname = $this->request->post['lastname'];
					} else {
						$firstname = $this->request->post['shipping_firstname'];
						$lastname = $this->request->post['shipping_lastname'];
					}

					if ($shipping_country_info) {
						$country = $shipping_country_info['name'];
						$iso_code_2 = $shipping_country_info['iso_code_2'];
						$iso_code_3 = $shipping_country_info['iso_code_3'];
						$address_format = $shipping_country_info['address_format'];
					} else {
						$country = '';
						$iso_code_2 = '';
						$iso_code_3 = '';
						$address_format = '';
					}

					$this->load->model('localisation/zone');
					$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);

					if ($zone_info) {
						$zone = $zone_info['name'];
						$zone_code = $zone_info['code'];
					} else {
						$zone = '';
						$zone_code = '';
					}

					$shipping_address_data = [
						'address_id'     => $address_id,
						'firstname'      => $firstname,
						'lastname'       => $lastname,
						'company'        => $this->request->post['shipping_company'],
						'address_1'      => $this->request->post['shipping_address_1'],
						'address_2'      => $this->request->post['shipping_address_2'],
						'city'           => $this->request->post['shipping_city'],
						'postcode'       => $this->request->post['shipping_postcode'],
						'zone_id'        => $this->request->post['shipping_zone_id'],
						'zone'           => $zone,
						'zone_code'      => $zone_code,
						'country_id'     => $this->request->post['shipping_country_id'],
						'country'        => $country,
						'iso_code_2'     => $iso_code_2,
						'iso_code_3'     => $iso_code_3,
						'address_format' => $address_format,
						'custom_field'   => isset($this->request->post['shipping_custom_field']) ? $this->request->post['shipping_custom_field'] : []
					];

					// Add
					if ($this->request->post['account']) {
						if (!$this->config->get('config_checkout_address')) {
							$shipping_address_data['default'] = 1;
						}
						$shipping_address_data['address_id'] = $this->model_account_address->addAddress($customer_data['customer_id'], $shipping_address_data);
					}

					// Edit
					if ($this->customer->isLogged() && $shipping_address_data['address_id']) {
						$this->model_account_address->editAddress($shipping_address_data['address_id'], $shipping_address_data);
					}

					// Requires Approval
					if (!$customer_group_info['approval']) {
						$this->session->data['shipping_address'] = $shipping_address_data;
					}
				} elseif (!$customer_group_info['approval'] && $this->config->get('config_checkout_address')) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];

					// Remove the address id so if the customer changes their mind and requires changing a different shipping address it will create a new address.
					$this->session->data['shipping_address']['address_id'] = 0;
				}
			}

			// If everything good login
			if (!$customer_group_info['approval']) {
				if ($this->request->post['account']) {
					$this->customer->login($this->request->post['email'], $this->request->post['password']);

					// Create customer token

					if(VERSION>='4.0.2.0'){
						$this->session->data['customer_token'] = oc_token(26);
					}else{
						$this->session->data['customer_token'] = Helper\General\token(26);
					}
					
					$json['success'] = $this->language->get('text_success_add');
				} elseif ($this->customer->isLogged()) {
					$json['success'] = $this->language->get('text_success_edit');
				} else {
					$json['success'] = $this->language->get('text_success_add');
				}

				unset($this->session->data['payment_methods']);
				unset($this->session->data['shipping_methods']);
			} else {
				// If account needs approval we redirect to the account success / requires approval page.
				$json['redirect'] = $this->url->link('account/success', 'language=' . $this->config->get('config_language'), true);
			}

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function save(): void {
		$this->load->language('checkout/register');
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');

		$json = [];

		$keys = [
			'account',
			'customer_group_id',
			'firstname',
			'lastname',
			'email',
			'telephone',
			'payment_company',
			'payment_address_1',
			'payment_address_2',
			'payment_city',
			'payment_postcode',
			'payment_country_id',
			'payment_zone_id',
			'payment_custom_field',
			'address_match',
			'shipping_firstname',
			'shipping_lastname',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_postcode',
			'shipping_country_id',
			'shipping_zone_id',
			'shipping_custom_field',
			'password',
			'agree'
		];

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		// Force account requires subscript or is a downloadable product.
		if ($this->cart->hasDownload() || $this->cart->hasSubscription()) {
			$this->request->post['account'] = 1;
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			if (!$product['minimum']) {
				$json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);

				break;
			}
		}

		if (!$json) {
			// If not guest checkout disabled, login require price or cart has downloads
			if (!$this->request->post['account'] && (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price'))) {
				$json['error']['warning'] = $this->language->get('error_guest');
			}

			// Customer Group
			if ($this->request->post['customer_group_id']) {
				$customer_group_id = (int)$this->request->post['customer_group_id'];
			} else {
				$customer_group_id = (int)$this->config->get('config_customer_group_id');
			}

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if (!$customer_group_info || !in_array($customer_group_id, (array)$this->config->get('config_customer_group_display'))) {
				$json['error']['warning'] = $this->language->get('error_customer_group');
			}

			if ((oc_strlen($this->request->post['firstname']) < 1) || (oc_strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			$tmdbirthday_status 		= $this->config->get('module_tmdbirthday_status');
			if($tmdbirthday_status) { 
				$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
				if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'])) {
					$error_dateofbirth = $tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'];
				} else {
					$error_dateofbirth = $this->language->get('error_dateofbirth');
				}
				if ((oc_strlen($this->request->post['date_of_birth']) < 1) || (oc_strlen($this->request->post['date_of_birth']) > 32)) {
					$json['error']['birthday'] = $error_dateofbirth;
				}
			}

		
			if ((oc_strlen($this->request->post['lastname']) < 1) || (oc_strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((oc_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			$this->load->model('account/customer');

			if ($this->request->post['account'] && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			// Logged
			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

				if ($customer_info['customer_id'] != $this->customer->getId()) {
					$json['error']['warning'] = $this->language->get('error_exists');
				}
			}

			if ($this->config->get('config_telephone_required') && (oc_strlen($this->request->post['telephone']) < 3) || (oc_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
					}
				}
			}

			if ($this->config->get('config_checkout_payment_address')) {
				if ((oc_strlen($this->request->post['payment_address_1']) < 3) || (oc_strlen($this->request->post['payment_address_1']) > 128)) {
					$json['error']['payment_address_1'] = $this->language->get('error_address_1');
				}

				if ((oc_strlen($this->request->post['payment_city']) < 2) || (oc_strlen($this->request->post['payment_city']) > 128)) {
					$json['error']['payment_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$payment_country_info = $this->model_localisation_country->getCountry((int)$this->request->post['payment_country_id']);

				if ($payment_country_info && $payment_country_info['postcode_required'] && (oc_strlen($this->request->post['payment_postcode']) < 2 || oc_strlen($this->request->post['payment_postcode']) > 10)) {
					$json['error']['payment_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['payment_country_id'] == '') {
					$json['error']['payment_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['payment_zone_id'] == '') {
					$json['error']['payment_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['payment_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['payment_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}

			if ($this->cart->hasShipping() && !$this->request->post['address_match']) {
				// If payment address not required we need to use the firstname and lastname from the account.
				if ($this->config->get('config_checkout_payment_address')) {
					if ((oc_strlen($this->request->post['shipping_firstname']) < 1) || (oc_strlen($this->request->post['shipping_firstname']) > 32)) {
						$json['error']['shipping_firstname'] = $this->language->get('error_firstname');
					}

					if ((oc_strlen($this->request->post['shipping_lastname']) < 1) || (oc_strlen($this->request->post['shipping_lastname']) > 32)) {
						$json['error']['shipping_lastname'] = $this->language->get('error_lastname');
					}
				}

				if ((oc_strlen($this->request->post['shipping_address_1']) < 3) || (oc_strlen($this->request->post['shipping_address_1']) > 128)) {
					$json['error']['shipping_address_1'] = $this->language->get('error_address_1');
				}

				if ((oc_strlen($this->request->post['shipping_city']) < 2) || (oc_strlen($this->request->post['shipping_city']) > 128)) {
					$json['error']['shipping_city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$shipping_country_info = $this->model_localisation_country->getCountry((int)$this->request->post['shipping_country_id']);

				if ($shipping_country_info && $shipping_country_info['postcode_required'] && (oc_strlen($this->request->post['shipping_postcode']) < 2 || oc_strlen($this->request->post['shipping_postcode']) > 10)) {
					$json['error']['shipping_postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['shipping_country_id'] == '') {
					$json['error']['shipping_country'] = $this->language->get('error_country');
				}

				if ($this->request->post['shipping_zone_id'] == '') {
					$json['error']['shipping_zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						if ($custom_field['required'] && empty($this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['shipping_custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping_custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
						}
					}
				}
			}

			// If account register password required
			if ($this->request->post['account'] && (oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['account']) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

				if ($information_info && !$this->request->post['agree']) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			// Captcha
			$this->load->model('setting/extension');

			if (!$this->customer->isLogged()) {
				$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

				if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
					$captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '.validate');

					if ($captcha) {
						$json['error']['captcha'] = $captcha;
					}
				}
			}
		}

		if (!$json) {
			// Add customer details into session
			$customer_data = [
				'customer_id'       => 0,
				'customer_group_id' => $customer_group_id,
				'firstname'         => $this->request->post['firstname'],
				'lastname'          => $this->request->post['lastname'],
				'email'             => $this->request->post['email'],
				'telephone'         => $this->request->post['telephone'],
				'custom_field'      => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : []
			];

			// Register
			if ($this->request->post['account']) {
				$customer_data['customer_id'] = $this->model_account_customer->addCustomer($this->request->post);
			}

			// Logged so edit customer details
			if ($this->customer->isLogged()) {
				$this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);
			}

			// Check if current customer group requires approval
			if (!$customer_group_info['approval']) {
				$this->session->data['customer'] = $customer_data;
			}

			$this->load->model('account/address');

			// Payment Address
			if ($this->config->get('config_checkout_payment_address')) {
				if (isset($this->session->data['payment_address']['address_id'])) {
					$address_id = $this->session->data['payment_address']['address_id'];
				} else {
					$address_id = 0;
				}

				if ($payment_country_info) {
					$country = $payment_country_info['name'];
					$iso_code_2 = $payment_country_info['iso_code_2'];
					$iso_code_3 = $payment_country_info['iso_code_3'];
					$address_format = $payment_country_info['address_format'];
				} else {
					$country = '';
					$iso_code_2 = '';
					$iso_code_3 = '';
					$address_format = '';
				}

				$this->load->model('localisation/zone');

				$zone_info = $this->model_localisation_zone->getZone($this->request->post['payment_zone_id']);

				if ($zone_info) {
					$zone = $zone_info['name'];
					$zone_code = $zone_info['code'];
				} else {
					$zone = '';
					$zone_code = '';
				}

				$payment_address_data = [
					'address_id'     => $address_id,
					'firstname'      => $this->request->post['firstname'],
					'lastname'       => $this->request->post['lastname'],
					'company'        => $this->request->post['payment_company'],
					'address_1'      => $this->request->post['payment_address_1'],
					'address_2'      => $this->request->post['payment_address_2'],
					'city'           => $this->request->post['payment_city'],
					'postcode'       => $this->request->post['payment_postcode'],
					'zone_id'        => $this->request->post['payment_zone_id'],
					'zone'           => $zone,
					'zone_code'      => $zone_code,
					'country_id'     => $this->request->post['payment_country_id'],
					'country'        => $country,
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format,
					'custom_field'   => isset($this->request->post['payment_custom_field']) ? $this->request->post['payment_custom_field'] : []
				];

				// Add
				if ($this->request->post['account']) {
					$payment_address_data['default'] = 1;

					$payment_address_data['address_id'] = $this->model_account_address->addAddress($customer_data['customer_id'], $payment_address_data);
				}

				// Edit
				if ($this->customer->isLogged() && $payment_address_data['address_id']) {
					$this->model_account_address->editAddress($payment_address_data['address_id'], $payment_address_data);
				}

				// Requires Approval
				if (!$customer_group_info['approval']) {
					$this->session->data['payment_address'] = $payment_address_data;
				}
			}

			// Shipping Address
			if ($this->cart->hasShipping()) {
				if (!$this->request->post['address_match']) {
					if (isset($this->session->data['shipping_address']['address_id'])) {
						$address_id = $this->session->data['shipping_address']['address_id'];
					} else {
						$address_id = 0;
					}

					if (!$this->config->get('config_checkout_payment_address')) {
						$firstname = $this->request->post['firstname'];
						$lastname = $this->request->post['lastname'];
					} else {
						$firstname = $this->request->post['shipping_firstname'];
						$lastname = $this->request->post['shipping_lastname'];
					}

					if ($shipping_country_info) {
						$country = $shipping_country_info['name'];
						$iso_code_2 = $shipping_country_info['iso_code_2'];
						$iso_code_3 = $shipping_country_info['iso_code_3'];
						$address_format = $shipping_country_info['address_format'];
					} else {
						$country = '';
						$iso_code_2 = '';
						$iso_code_3 = '';
						$address_format = '';
					}

					$this->load->model('localisation/zone');

					$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);

					if ($zone_info) {
						$zone = $zone_info['name'];
						$zone_code = $zone_info['code'];
					} else {
						$zone = '';
						$zone_code = '';
					}

					$shipping_address_data = [
						'address_id'     => $address_id,
						'firstname'      => $firstname,
						'lastname'       => $lastname,
						'company'        => $this->request->post['shipping_company'],
						'address_1'      => $this->request->post['shipping_address_1'],
						'address_2'      => $this->request->post['shipping_address_2'],
						'city'           => $this->request->post['shipping_city'],
						'postcode'       => $this->request->post['shipping_postcode'],
						'zone_id'        => $this->request->post['shipping_zone_id'],
						'zone'           => $zone,
						'zone_code'      => $zone_code,
						'country_id'     => $this->request->post['shipping_country_id'],
						'country'        => $country,
						'iso_code_2'     => $iso_code_2,
						'iso_code_3'     => $iso_code_3,
						'address_format' => $address_format,
						'custom_field'   => isset($this->request->post['shipping_custom_field']) ? $this->request->post['shipping_custom_field'] : []
					];

					// Add
					if ($this->request->post['account']) {
						if (!$this->config->get('config_checkout_payment_address')) {
							$shipping_address_data['default'] = 1;
						}

						$shipping_address_data['address_id'] = $this->model_account_address->addAddress($customer_data['customer_id'], $shipping_address_data);
					}

					// Edit
					if ($this->customer->isLogged() && $shipping_address_data['address_id']) {
						$this->model_account_address->editAddress($shipping_address_data['address_id'], $shipping_address_data);
					}

					// Requires Approval
					if (!$customer_group_info['approval']) {
						$this->session->data['shipping_address'] = $shipping_address_data;
					}
				} elseif (!$customer_group_info['approval'] && $this->config->get('config_checkout_payment_address')) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];

					// Remove the address id so if the customer changes their mind and requires changing a different shipping address it will create a new address.
					$this->session->data['shipping_address']['address_id'] = 0;
				}
			}

			// If everything good login
			if (!$customer_group_info['approval']) {
				if ($this->request->post['account']) {
					$this->customer->login($this->request->post['email'], $this->request->post['password']);

					// Create customer token
					if(VERSION>='4.0.2.0'){
						$this->session->data['customer_token'] = oc_token(26);
					}else{
						$this->session->data['customer_token'] = Helper\General\token(26);
					}

					$json['success'] = $this->language->get('text_success_add');
				} elseif ($this->customer->isLogged()) {
					$json['success'] = $this->language->get('text_success_edit');
				} else {
					$json['success'] = $this->language->get('text_success_guest');
				}
			} else {
				// If account needs approval we redirect to the account success / requires approval page.
				$json['redirect'] = $this->url->link('account/success', 'language=' . $this->config->get('config_language'), true);
			}

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function accountregisterform(string&$route, array&$args, mixed&$output):void {
		
		$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		$this->load->model('extension/tmdbirthdayreminder/tmd/tmdbirthday');
		
		$this->load->language('account/register');
		
		$args['tmdbirthday_status'] = $this->config->get('module_tmdbirthday_status');
		
		$tmdbirthday_status = $this->config->get('module_tmdbirthday_status');
		if($tmdbirthday_status) { 
			if (isset($this->request->post['date_of_birth'])) {
				$args['date_of_birth'] = $this->request->post['date_of_birth'];
			} else {
				$args['date_of_birth'] = '';
			}

			$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
			if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['date_of_birth'])) {
				$args['entry_birthday'] = $tmdbirthday_language[$this->config->get('config_language_id')]['date_of_birth'];
			} else {
				$args['entry_birthday'] = $this->language->get('entry_birthday');
			}

			if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['dob_necessarytext'])) {
				$args['text_correctdate'] = $tmdbirthday_language[$this->config->get('config_language_id')]['dob_necessarytext'];
			} else {
				$args['text_correctdate'] = $this->language->get('text_correctdate');
			}
		}
					
		if(VERSION>='4.0.2.0'){
			$args['register'] = $this->url->link('extension/tmdbirthdayreminder/tmd/tmdcalender.register', 'language=' . $this->config->get('config_language') . '&register_token=' . $this->session->data['register_token']);
		}else{
			$args['register'] = $this->url->link('extension/tmdbirthdayreminder/tmd/tmdcalender|register', 'language=' . $this->config->get('config_language') . '&register_token=' . $this->session->data['register_token']);
		}
			
			$template_buffer = $this->getTemplateBuffer($route, $output);
			$find            = '{% if config_telephone_display %}';
			$replace         = '{% if tmdbirthday_status %}<div class="row mb-3 required">
            <label for="input-birthday" class="col-sm-2 col-form-label">{{ entry_birthday }}</label>
            <div class="col-sm-10">
			<div class="input-group">
			<input type="text" name="date_of_birth" value="{{ date_of_birth }}" placeholder="{{ entry_birthday }}" id="input-birthday" class="form-control date"/>
                <div class="input-group-text"><i class="fa-regular fa-calendar"></i></div>
                <div id="error-birthday" class="invalid-feedback"></div>
              </div>
			  <div class="float-end">
			  <i>{{ text_correctdate }}</i>
			  </div>
			  </div>
			  </div>{% endif %}'.'{% if config_telephone_display %}';
			  $output = str_replace($find, $replace, $template_buffer);
			  
			}
		
	
			public function register(): void {
				$this->load->language('account/register');
				$this->load->language('extension/tmdbirthdayreminder/module/tmdbirthday');
		
				$json = [];
		
				$keys = [
					'customer_group_id',
					'firstname',
					'lastname',
					'email',
					'telephone',
					'custom_field',
					'password',
					'confirm',
					'agree'
				];
		
				foreach ($keys as $key) {
					if (!isset($this->request->post[$key])) {
						$this->request->post[$key] = '';
					}
				}
		
				if (!isset($this->request->get['register_token']) || !isset($this->session->data['register_token']) || ($this->session->data['register_token'] != $this->request->get['register_token'])) {
					$json['redirect'] = $this->url->link('account/register', 'language=' . $this->config->get('config_language'), true);
				}
		
				if (!$json) {
					// Customer Group
					if ($this->request->post['customer_group_id']) {
						$customer_group_id = (int)$this->request->post['customer_group_id'];
					} else {
						$customer_group_id = (int)$this->config->get('config_customer_group_id');
					}
		
					$this->load->model('account/customer_group');
		
					$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
		
					
					if (!$customer_group_info || !in_array($customer_group_id, (array)$this->config->get('config_customer_group_display'))) {
						$json['error']['warning'] = $this->language->get('error_customer_group');
					}
					if(VERSION>='4.0.2.0'){
		
					if ((oc_strlen($this->request->post['firstname']) < 1) || (oc_strlen($this->request->post['firstname']) > 32)) {
						$json['error']['firstname'] = $this->language->get('error_firstname');
					}

					$tmdbirthday_status = $this->config->get('module_tmdbirthday_status');
					if($tmdbirthday_status) { 
						$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
						if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'])) {
							$error_date_of_birth = $tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'];
						} else {
							$error_date_of_birth = $this->language->get('error_dateofbirth');
						}
					  if ((oc_strlen($this->request->post['date_of_birth']) < 1) || (oc_strlen($this->request->post['date_of_birth']) > 32)) {
							$json['error']['birthday'] = $error_date_of_birth;
						}
					}
				  
		
					if ((oc_strlen($this->request->post['lastname']) < 1) || (oc_strlen($this->request->post['lastname']) > 32)) {
						$json['error']['lastname'] = $this->language->get('error_lastname');
					}
		
					if ((oc_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
						$json['error']['email'] = $this->language->get('error_email');
					}
				}else{

					if ((Helper\Utf8\strlen($this->request->post['firstname']) < 1) || (Helper\Utf8\strlen($this->request->post['firstname']) > 32)) {
						$json['error']['firstname'] = $this->language->get('error_firstname');
					}

					$tmdbirthday_status = $this->config->get('module_tmdbirthday_status');
					if($tmdbirthday_status) { 
						$tmdbirthday_language = $this->config->get('module_tmdbirthday_language');
						if(!empty($tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'])) {
							$error_date_of_birth = $tmdbirthday_language[$this->config->get('config_language_id')]['error_date_of_birth'];
						} else {
							$error_date_of_birth = $this->language->get('error_dateofbirth');
						}
						if ((Helper\Utf8\strlen($this->request->post['date_of_birth']) < 1) || (Helper\Utf8\strlen($this->request->post['date_of_birth']) > 32)) {
							$json['error']['birthday'] = $error_date_of_birth;
						}
					}
		
					if ((Helper\Utf8\strlen($this->request->post['lastname']) < 1) || (Helper\Utf8\strlen($this->request->post['lastname']) > 32)) {
						$json['error']['lastname'] = $this->language->get('error_lastname');
					}
		
					if ((Helper\Utf8\strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
						$json['error']['email'] = $this->language->get('error_email');
					}

				}
		
					$this->load->model('account/customer');
		
					if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
						$json['error']['warning'] = $this->language->get('error_exists');
					}
		
					if(VERSION>='4.0.2.0'){
					if ($this->config->get('config_telephone_required') && (oc_strlen($this->request->post['telephone']) < 3) || (oc_strlen($this->request->post['telephone']) > 32)) {
						$json['error']['telephone'] = $this->language->get('error_telephone');
					}

				}else{

					if ($this->config->get('config_telephone_required') && (Helper\Utf8\strlen($this->request->post['telephone']) < 3) || (Helper\Utf8\strlen($this->request->post['telephone']) > 32)) {
						$json['error']['telephone'] = $this->language->get('error_telephone');
					}
				}
		
					// Custom field validation
					$this->load->model('account/custom_field');
		
					$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
		
					foreach ($custom_fields as $custom_field) {
						if ($custom_field['location'] == 'account') {
							if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
								$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
							} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['custom_field_id']])) {
								$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
							}
						}
					}
		
					if(VERSION>='4.0.2.0'){
					if ((oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
						$json['error']['password'] = $this->language->get('error_password');
					}
					}else{
						if ((Helper\Utf8\strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (Helper\Utf8\strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
							$json['error']['password'] = $this->language->get('error_password');
						}
			
					}
		
					// Captcha
					$this->load->model('setting/extension');
		
					$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));
		
					if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
						$captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '.validate');
		
						if ($captcha) {
							$json['error']['captcha'] = $captcha;
						}
					}
		
					// Agree to terms
					$this->load->model('catalog/information');
		
					$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
		
					if ($information_info && !$this->request->post['agree']) {
						$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
					}
				}
		
				if (!$json) {
					$customer_id = $this->model_account_customer->addCustomer($this->request->post);
		
					// Login if requires approval
					if (!$customer_group_info['approval']) {
						$this->customer->login($this->request->post['email'], html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8'));
		
						// Add customer details into session
						$this->session->data['customer'] = [
							'customer_id'       => $customer_id,
							'customer_group_id' => $customer_group_id,
							'firstname'         => $this->request->post['firstname'],
							'lastname'          => $this->request->post['lastname'],
							'email'             => $this->request->post['email'],
							'telephone'         => $this->request->post['telephone'],
							'custom_field'      => $this->request->post['custom_field']
						];
		
						// Log the IP info
						$this->model_account_customer->addLogin($this->customer->getId(), $this->request->server['REMOTE_ADDR']);
		
						// Create customer token
						if(VERSION>='4.0.2.0'){
							$this->session->data['customer_token'] = oc_token(26);
						}else{
							$this->session->data['customer_token'] = Helper\General\token(26);
						}
					}
		
					// Clear any previous login attempts for unregistered accounts.
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
		
					unset($this->session->data['guest']);
					unset($this->session->data['register_token']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
		
					$json['redirect'] = $this->url->link('account/success', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''), true);
				}
		
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
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

	protected function getTemplateBuffer( $route, $event_template_buffer ) {
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
		$dir_template = DIR_TEMPLATE ;
			
		
		$template_file = $dir_template . $route . '.twig';
		if (file_exists( $template_file ) && is_file( $template_file )) {
			return file_get_contents( $template_file );
		}
		if ($this->isCatalog()) {
			trigger_error("Cannot find template file for route '$route'");
			exit;
		}
		$dir_template = DIR_TEMPLATE . 'default/template/';
		$template_file = $dir_template . $route . '.twig';
		if (file_exists( $template_file ) && is_file( $template_file )) {
			return file_get_contents( $template_file );
		}
		trigger_error("Cannot find template file for route '$route'");
		exit;
	}
}