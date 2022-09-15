<?php
/**
 * mypos_virtual.php payment module class for myPOS Virtual Payment module
 */

/**
 * mypos_virtual.php payment module class for myPOS Virtual Payment module
 */
class mypos_virtual extends base
{
    private $_defaultMethodProperties = array(
        'IPCmethod' => null,
        'KeyIndex' => null,
        'IPCVersion' => null,
        'IPCLanguage' => null,
        'WalletNumber' => null,
        'SID' => null
        //'Signature' is added last, because of the API requirements
    );

    /**
     * string representing the payment method
     *
     * @var string
     */
    var $code;
    /**
     * $title is the displayed name for this payment method
     *
     * @var string
     */
    var $title;
    /**
     * $description is a soft name for this payment method
     *
     * @var string
     */
    var $description;
    /**
     * $enabled determines whether this module shows or not... in catalog.
     *
     * @var boolean
     */
    var $enabled;

    /**
     * constructor
     *
     * @return mypos_virtual
     */
    function mypos_virtual()
    {
        global $order, $messageStack;
        $this->code = 'mypos_virtual';
        $this->codeVersion = '1.0.2';
        $this->api_version = '1.0';

        if (IS_ADMIN_FLAG === true) {
            // Payment Module title in Admin
            $this->title = 'myPOS Virtual';
        }
        else {
            $this->title = MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_CATALOG_TITLE; // Payment Module title in Catalog
        }
        $this->description = MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_MYPOS_VIRTUAL_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS == 'True') ? true : false);

        if (is_object($order)) {
            $this->update_status();
        }

        if (PROJECT_VERSION_MAJOR != '1' && substr(PROJECT_VERSION_MINOR, 0, 3) != '5.4') {
            $this->enabled = false;
        }

        $this->private_key = MODULE_PAYMENT_MYPOS_VIRTUAL_PRIVATE_KEY;
        $this->public_certificate = MODULE_PAYMENT_MYPOS_VIRTUAL_PUBLIC_CERTIFICATE;

        $this->_defaultMethodProperties['KeyIndex'] = MODULE_PAYMENT_MYPOS_VIRTUAL_KEYINDEX;
        $this->_defaultMethodProperties['IPCVersion'] = $this->api_version;
        $this->_defaultMethodProperties['IPCLanguage'] = 'EN';
        $this->_defaultMethodProperties['WalletNumber'] = MODULE_PAYMENT_MYPOS_VIRTUAL_WALLET_NR;
        $this->_defaultMethodProperties['SID'] = MODULE_PAYMENT_MYPOS_VIRTUAL_SITE_ID;
        $this->_defaultMethodProperties['Source'] = 'sc_zencart';

        $this->form_action_url = MODULE_PAYMENT_MYPOS_VIRTUAL_TEST_MODE == 'True' ? MODULE_PAYMENT_MYPOS_VIRTUAL_DEVELOPER_URL : MODULE_PAYMENT_MYPOS_VIRTUAL_PRODUCTION_URL;

        // verify table structure
        if (IS_ADMIN_FLAG === true) {
            $this->tableCheckup();
        }
    }

    /**
     * calculate zone matches and flag settings to determine whether this module should display to customers or not
     *
     */
    function update_status()
    {
        global $order, $db;
    }

    /**
     * JS validation which does error-checking of data-entry if this module is selected for use
     * (Number, Owner, and CVV Lengths)
     *
     * @return string
     */
    function javascript_validation()
    {
        return false;
    }

    /**
     * Displays payment method name along with Credit Card Information Submission Fields (if any) on the Checkout Payment Page
     *
     * @return array
     */
    function selection()
    {
        return array('id' => $this->code,
            'module' => MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_CATALOG_LOGO,
            'icon' => MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_CATALOG_LOGO
        );
    }

    /**
     * Normally evaluates the Credit Card Type for acceptance and the validity of the Credit Card Number & Expiration Date
     * Since myPOS Virtual module is not collecting info, it simply skips this step.
     *
     * @return boolean
     */
    function pre_confirmation_check()
    {
        global $cartID, $cart, $order;

        /**
         * @var shoppingCart $cart
         */
        $cart = $_SESSION['cart'];


        if (empty($cart->cartID)) {
            $cartID = $cart->cartID = $cart->generate_cart_id();
        } else {
            $cartID = $cart->cartID;
        }

        if (!isset($_SESSION['mypos_virtual']['cart_id'])) {
            $_SESSION['mypos_virtual']['cartID'] = $cartID;
        }
    }

    /**
     * Display Credit Card Information on the Checkout Confirmation Page
     * Since none is collected for myPOS Virtual before forwarding to myPOS Virtual site, this is skipped
     *
     * @return boolean
     */
    function confirmation()
    {
        return false;
    }

    /**
     * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
     * This sends the data to the payment gateway for processing.
     * (These are hidden fields on the checkout confirmation page)
     *
     * @return string
     */
    function process_button()
    {
               /**
         * Outputs the html form hidden elements sent as POST data to the payment gateway.
         * Called by checkout_confirmation.php
         */
        
        global $cart, $order, $order_totals, $order_total_modules, $db, $insert_id, $messageStack;
        
        $code = substr(str_shuffle(MD5(microtime())), 0, 12);
                
        //  save in weldpay table
        $sql = "INSERT INTO ".TABLE_MYPOS."
                SET code = '".$code."',
                order_data = '".addslashes(serialize($order))."',
                order_totals = '".addslashes(serialize($order_totals))."',
                order_total_modules = '".addslashes(serialize($order_total_modules))."'";
        $db->execute($sql);

        $cart_myPOS_Virtual_ID = $_SESSION['mypos_virtual']['cart_myPOS_Virtual_ID'];

        $params['securityToken'] = $_SESSION['securityToken'];
        //$params = array_merge($params, $this->_defaultMethodProperties);

        //$params['IPCLanguage'] = substr($_SESSION['languages_code'], 0, 2);

        $params['IPCmethod'] = 'IPCPurchase';
        $params['IPCVersion'] = '1.3';
        $params['IPCLanguage'] = 'EN';
        $params['SID'] = MODULE_PAYMENT_MYPOS_VIRTUAL_SITE_ID;
        //$params['WalletNumber'] = MODULE_PAYMENT_MYPOS_VIRTUAL_WALLET_NR;
        $params['walletnumber'] = MODULE_PAYMENT_MYPOS_VIRTUAL_WALLET_NR;
        $params['Amount'] = number_format($order->info['total'], 2, '.', '');
        $params['Currency'] = $order->info['currency'];
        //$params['OrderID'] = substr($cart_myPOS_Virtual_ID, strpos($cart_myPOS_Virtual_ID, '-')+1);;
        $params['OrderID'] = $code;
        $params['URL_OK'] = html_entity_decode(HTTPS_SERVER.DIR_WS_CATALOG."mypos_virtual_handler.php?mypos_code=".$code);
        $params['URL_Cancel'] = html_entity_decode(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
        $params['URL_Notify'] = HTTPS_SERVER . DIR_WS_CATALOG . 'mypos_virtual_handler.php';
        $params['CardTokenRequest'] = '0';
        $params['KeyIndex'] = MODULE_PAYMENT_MYPOS_VIRTUAL_KEYINDEX;
        $params['PaymentParametersRequired'] = '1';
        //$params['CustomerEmail'] = $order->customer['email_address'];
        $params['customeremail'] = $order->customer['email_address'];
        //$params['CustomerFirstNames'] =  $order->customer['firstname'];
        $params['customerfirstnames'] =  $order->customer['firstname'];
        //$params['CustomerFamilyName'] = $order->customer['lastname'];
        $params['customerfamilyname'] = $order->customer['lastname'];
        //$params['CustomerPhone'] = $order->customer['telephone'];
        $params['customerphone'] = $order->customer['telephone'];
        //$params['CustomerCountry'] = $order->customer['country']['iso_code_3'];
        $params['customercountry'] = $order->customer['country']['iso_code_3'];
        //$params['CustomerCity'] = $order->customer['city'];
        $params['customercity'] = urlencode($order->customer['city']);
        //$params['CustomerZIPCode'] = $order->customer['postcode'];
        $params['customerzipcode'] = $order->customer['postcode'];
        //$params['CustomerAddress'] = $order->customer['street_address'];
        $params['customeraddress'] = $order->customer['street_address'];
        $params['Note'] = 'myPOS Virtual ZenCart Extension';
        $params['CartItems'] = count($order->products) + 1;
        
        //$params['CustomerIP'] = $_SERVER['REMOTE_ADDR'];

        $index = 1;

        /**
         * @var array $item
         */
        foreach($order->products as $item)
        {
            $params['Article_' . $index] = urlencode(addslashes(strip_tags($item['name'])));
            $params['Quantity_' . $index] = number_format($item['qty'], 0, '.', '');
            $params['Price_' . $index] = number_format($item['final_price'], 2, '.', '');
            $params['Amount_' . $index] = number_format($item['final_price'] * $item['qty'], 2, '.', '');
            $params['Currency_' . $index] = $order->info['currency'];

            $index++;
        }

        if (!empty($order->info['tax_groups'])) {
            foreach ($order->info['tax_groups'] as $name => $amount) {
                if ($amount != 0) {
                    $params['Article_' . $index] = urlencode($name);
                    $params['Quantity_' . $index] = number_format(1, 0, '.', '');
                    $params['Price_' . $index] = number_format($amount, 2, '.', '');
                    $params['Amount_' . $index] = number_format($amount, 2, '.', '');
                    $params['Currency_' . $index] = $order->info['currency'];
                    $params['CartItems']++;
                    $index++;
                }
            }
        }

        if (isset($order->info['coupon_code'])) {
            $couponCode = $order->info['coupon_code'];

            $check_query = $db->Execute("select coupon_amount from " . TABLE_COUPONS . " where coupon_code = '" . $couponCode . "'");

            if ($check_query->RecordCount() !=  0) {
                $coupon = $check_query->current();

                if ($coupon['coupon_amount'] != 0) {
                    $params['Article_' . $index] = MODULE_ORDER_TOTAL_COUPON_TITLE . ': ' . $couponCode;
                    $params['Quantity_' . $index] = number_format(1, 0, '.', '');
                    $params['Price_' . $index] = number_format(-$coupon['coupon_amount'], 2, '.', '');
                    $params['Amount_' . $index] = number_format(-$coupon['coupon_amount'], 2, '.', '');
                    $params['Currency_' . $index] = $order->info['currency'];
                    $params['CartItems']++;
                    $index++;
                }
            }
        }

        $params['Article_' . $index] = $order->info['shipping_method'];
        $params['Quantity_' . $index] = number_format(1, 0, '.', '');
        $params['Price_' . $index] = number_format($order->info['shipping_cost'], 2, '.', '');
        $params['Amount_' . $index] = number_format($order->info['shipping_cost'], 2, '.', '');
        $params['Currency_' . $index] = $order->info['currency'];

        $params['Signature'] = $this->create_signature($params);
//echo '<pre>'; print_r($params); echo '</pre>'; exit();
        unset($params['securityToken']);

        $process_button_string = '';

        foreach ($params as $key => $value) {
            $process_button_string .= zen_draw_hidden_field($key, $value);
        }

        return $process_button_string;
    }

    /**
     * Store transaction info to the order and process any results that come back from the payment gateway
     */
    function before_process()
    {
        return false;
    }

    /**
     * Checks referrer
     *
     * @param string $zf_domain
     * @return boolean
     */
    function check_referrer($zf_domain)
    {
        return true;
    }

    /**
     * Build admin-page components
     *
     * @param int $zf_order_id
     * @return string
     */
    function admin_notification($zf_order_id)
    {
        global $db;
        $output = '';
        return $output;
    }

    /**
     * Post-processing activities
     * When the order returns from the processor, if PDT was successful, this stores the results in order-status-history and logs data for subsequent reference
     *
     * @return boolean
     */
    function after_process()
    {
        global $insert_id, $db, $order;
    }

    /**
     * Used to display error message details
     *
     * @return boolean
     */
    function output_error()
    {
        return false;
    }

    /**
     * Check to see whether module is installed
     *
     * @return boolean
     */
    function check()
    {
        global $db;

        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS'");
            $this->_check = $check_query->RecordCount();
        }

        return $this->_check;
    }

    /**
     * Install the payment module and its configuration settings
     *
     */
    function install()
    {
        global $db, $messageStack;

        if (defined('MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS')) {
            $messageStack->add_session('myPOS VIrtual module already installed.', 'error');
            zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=mypos_virtual', 'NONSSL'));
            return 'failed';
        }

        // STATUS
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable myPOS Virtual', 'MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS', 'True', 'Enable myPOS Virtual Payment Method', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        // TEST MODE
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test Mode', 'MODULE_PAYMENT_MYPOS_VIRTUAL_TEST_MODE', 'True', 'In order to test integration with myPOS Virtual you need to use the testing data available in your online banking at www.mypos.eu > menu Online > Shopping carts > Zen Cart.', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        // SORT ORDER
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_MYPOS_VIRTUAL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");

        // SITE ID
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Store ID', 'MODULE_PAYMENT_MYPOS_VIRTUAL_SITE_ID', '', 'Store ID is given when you add a new online store. It could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores.', '6', '6', now())");

        // WALLET NUMBER
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Client Number', 'MODULE_PAYMENT_MYPOS_VIRTUAL_WALLET_NR', '', 'You can view your myPOS Client number in your online banking at www.mypos.eu', '6', '8', now())");

        // PRIVATE KEY
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Private Key', 'MODULE_PAYMENT_MYPOS_VIRTUAL_PRIVATE_KEY', '', 'The Private Key for your store is generated in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', '6', '10', now())");

        // PUBLIC CERTIFICATE
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('myPOS Public Certificate', 'MODULE_PAYMENT_MYPOS_VIRTUAL_PUBLIC_CERTIFICATE', '', 'The myPOS Public Certificate is available for download in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', '6', '14', now())");

        // KEY INDEX
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Key Index', 'MODULE_PAYMENT_MYPOS_VIRTUAL_KEYINDEX', '', 'The Key Index assigned to the certificate could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', '6', '16', now())");

        // DEVELOPER URL
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Developer URL', 'MODULE_PAYMENT_MYPOS_VIRTUAL_DEVELOPER_URL', 'https://www.mypos.eu/vmp/checkout-test', '', '6', '18', now())");

        // PRODUCTION URL
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Production URL', 'MODULE_PAYMENT_MYPOS_VIRTUAL_PRODUCTION_URL', 'https://www.mypos.eu/vmp/checkout', '', '6', '20', now())");

        if (!defined('TABLE_MYPOS')) define('TABLE_MYPOS', DB_PREFIX.'mypos');
      $db->execute("DROP TABLE IF EXISTS `".TABLE_MYPOS."`;");
      $db->execute("CREATE TABLE IF NOT EXISTS `".TABLE_MYPOS."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `order_data` text NOT NULL,
  `order_totals` text NOT NULL,
  `order_total_modules` text NOT NULL,
  UNIQUE KEY `idx_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

        $this->notify('NOTIFY_PAYMENT_MYPOS_VIRTUAL_INSTALLED');
    }

    /**
     * Remove the module and all its settings
     *
     */
    function remove()
    {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE\_PAYMENT\_MYPOS_VIRTUAL\_%'");
        $this->notify('NOTIFY_PAYMENT_MYPOS_VIRTUAL_UNINSTALLED');
    }

    /**
     * Internal list of configuration keys used for configuration of the module
     *
     * @return array
     */
    function keys()
    {
        $keys_list = array(
            'MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_TEST_MODE',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_SORT_ORDER',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_SITE_ID',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_WALLET_NR',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_PRIVATE_KEY',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_PUBLIC_CERTIFICATE',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_KEYINDEX',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_DEVELOPER_URL',
            'MODULE_PAYMENT_MYPOS_VIRTUAL_PRODUCTION_URL',
        );

        return $keys_list;
    }

    function tableCheckup()
    {
        global $db, $sniffer;
    }

    private function create_signature($post)
    {
        $this->private_key = str_replace('-----BEGIN RSA PRIVATE KEY----- ', '', $this->private_key);
        $this->private_key = str_replace(' -----END RSA PRIVATE KEY-----',   '', $this->private_key);

        $this->private_key = '-----BEGIN RSA PRIVATE KEY-----' . "\n" . $this->private_key;

        for ($i = 96; $i < strlen($this->private_key); $i += 65) {
            $this->private_key[$i] = "\n";
        }

        $this->private_key .= "\n" . '-----END RSA PRIVATE KEY-----';

        $concData = base64_encode(implode('-', $post));
        $privKeyObj = openssl_get_privatekey($this->private_key);
        openssl_sign($concData, $signature, $privKeyObj, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function is_valid_signature($post)
    {
        $this->public_certificate = str_replace('-----BEGIN CERTIFICATE----- ', '', $this->public_certificate);
        $this->public_certificate = str_replace(' -----END CERTIFICATE-----',   '', $this->public_certificate);

        $this->public_certificate = '-----BEGIN CERTIFICATE-----' . "\n" . $this->public_certificate;

        for ($i = 92; $i < strlen($this->public_certificate); $i += 65) {
            $this->public_certificate[$i] = "\n";
        }

        $this->public_certificate .= "\n" . '-----END CERTIFICATE-----';

        // Save signature
        $signature = $post['Signature'];

        // Remove signature from POST data array
        unset($post['Signature']);

        // Concatenate all values
        $concData = base64_encode(implode('-', $post));

        // Extract public key from certificate
        $pubKeyId = openssl_get_publickey($this->public_certificate);

        // Verify signature
        $result = openssl_verify($concData, base64_decode($signature), $pubKeyId, OPENSSL_ALGO_SHA256);

        //Free key resource
        openssl_free_key($pubKeyId);

        if ($result == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
