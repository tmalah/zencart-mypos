<?php
require('includes/application_top.php');

require('includes/modules/payment/mypos_virtual.php');

if (!defined('MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS') || (MODULE_PAYMENT_MYPOS_VIRTUAL_STATUS  != 'True')) {
    echo 'MODULE NOT INSTALLED';
    exit;
}

if (isset($_GET['mypos_code']) && $_GET['mypos_code'] != '') {

    $payment = new mypos_virtual();
    
    $result = $payment->is_valid_signature($_POST);
    
    if ($result) {
    
        if ($_POST['IPCmethod'] == 'IPCPurchaseNotify') {
    
            global $currencies, $db;
    
            $code = $_GET['mypos_code'];
        
            //  get order id
            $sql = "SELECT * FROM ".TABLE_MYPOS."
                    WHERE code = '".$code."'";
    
            $mypos_res = $db->execute($sql);
            
            if ($mypos_res->RecordCount() > 0) {
                
                require_once(DIR_WS_CLASSES.'order.php');
                require_once(DIR_WS_CLASSES . 'order_total.php');
                
                include_once(DIR_WS_LANGUAGES.$_SESSION['language'].'/checkout_process.php');
                
                $order = unserialize($mypos_res->fields['order_data']);
                $order_total_modules = unserialize($mypos_res->fields['order_total_modules']);
    //echo '<pre>'; print_r($order); echo '</pre>'; exit();
                $insert_id = $order->create(unserialize($mypos_res->fields['order_totals']), 2);
    
                // store the product info to the order
                $order->create_add_products($insert_id);
    
                //send email notifications
                $order->send_order_email($insert_id, 2);
                
                if (MODULE_PAYMENT_WELDPAY_ORDER_STATUS_ID == 0) {
                    $order_status = DEFAULT_ORDERS_STATUS_ID;
                } else {
                    $order_status = MODULE_PAYMENT_WELDPAY_ORDER_STATUS_ID;
                }
            
                //  set order status
                $sql = "UPDATE ".TABLE_ORDERS."
                        SET orders_status = ".$order_status."
                        WHERE orders_id = ".$insert_id;
                $db->execute($sql);
            
                //  set order status history
                $commentString = 'MyPOS code: '.$code.'; Transaction ID: ' . $_POST['IPC_Trnref'] . '; ';
                
                $sql_data_array= array(array('fieldName' => 'orders_id', 'value' => $insert_id, 'type' => 'integer'),
                                   array('fieldName' => 'orders_status_id', 'value' => $order_status, 'type' => 'integer'),
                                   array('fieldName' => 'date_added', 'value' => 'now()', 'type' => 'noquotestring'),
                                   array('fieldName' => 'customer_notified', 'value' => 0, 'type' => 'integer'),
                                   array('fieldName' => 'comments', 'value' => $commentString, 'type' => 'string'));
                $db->perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
                
                $_SESSION['cart']->reset(true);

                // unregister session variables used during checkout
                  unset($_SESSION['sendto']);
                  unset($_SESSION['billto']);
                  unset($_SESSION['shipping']);
                  unset($_SESSION['payment']);
                  unset($_SESSION['comments']);
                  //$order_total_modules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
                
                  // This should be before the zen_redirect:
                  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_PROCESS');
                
                  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, (isset($_GET['action']) && $_GET['action'] == 'confirm' ? 'action=confirm' : ''), 'SSL'));
                
            } else {
                echo 'Invalid myPOS code';
            }
            
        } else {
            echo 'INVALID METHOD';
        }
    } else {
        echo 'INVALID SIGNATURE';
    }
    
} else {
    
    echo 'WRONG REQUEST';
    
}

unset($_SESSION);
?>
