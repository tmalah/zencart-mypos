<?php
define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_ADMIN_TITLE', 'myPOS Checkout');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_ADMIN_TITLE_NONUSA', 'myPOS Checkout');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_CATALOG_TITLE', 'myPOS Checkout');

if (IS_ADMIN_FLAG === true) {
    define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_DESCRIPTION', '<strong>myPOS Checkout</strong><br/><br/><img src="images/icon_popup.gif" border="0" />&nbsp;<a href="https://mypos.eu/en/register" target="_blank" style="text-decoration: underline; font-weight: bold;">Sign up for a myPOS account</a>');
}
else {
    define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_DESCRIPTION', '<strong>myPOS Checkout</strong><br/><br/><img src="images/icon_popup.gif" border="0" />&nbsp;<a href="https://mypos.eu/en/register" target="_blank" style="text-decoration: underline; font-weight: bold;">Sign up for a myPOS account</a>');
}

define('MODULE_PAYMENT_MYPOS_VIRTUAL_MARK_BUTTON_IMG', 'https://www.mypos.eu/img/icons/logo.png');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_MARK_BUTTON_ALT', 'Checkout with myPOS Checkout');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ACCEPTANCE_MARK_TEXT', 'Save time. Checkout securely.');

define('MODULE_PAYMENT_MYPOS_VIRTUAL_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_MYPOS_VIRTUAL_MARK_BUTTON_IMG . '" alt="' . MODULE_PAYMENT_MYPOS_VIRTUAL_MARK_BUTTON_ALT . '" title="' . MODULE_PAYMENT_MYPOS_VIRTUAL_MARK_BUTTON_ALT . '" width="70"/> &nbsp;' .
    '<span class="smallText">' . MODULE_PAYMENT_MYPOS_VIRTUAL_ACCEPTANCE_MARK_TEXT . '</span>');

define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_FIRST_NAME', 'First Name:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_LAST_NAME', 'Last Name:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_BUSINESS_NAME', 'Business Name:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_NAME', 'Address Name:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_STREET', 'Address Street:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_CITY', 'Address City:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_STATE', 'Address State:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_ZIP', 'Address Zip:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_COUNTRY', 'Address Country:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_EMAIL_ADDRESS', 'Payer Email:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_EBAY_ID', 'Ebay ID:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYER_ID', 'Payer ID:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYER_STATUS', 'Payer Status:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_ADDRESS_STATUS', 'Address Status:');

define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYMENT_TYPE', 'Payment Type:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYMENT_STATUS', 'Payment Status:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PENDING_REASON', 'Pending Reason:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_INVOICE', 'Invoice:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYMENT_DATE', 'Payment Date:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_CURRENCY', 'Currency:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_GROSS_AMOUNT', 'Gross Amount:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PAYMENT_FEE', 'Payment Fee:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_EXCHANGE_RATE', 'Exchange Rate:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_CART_ITEMS', 'Cart items:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_TXN_TYPE', 'Trans. Type:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_TXN_ID', 'Trans. ID:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_PARENT_TXN_ID', 'Parent Trans. ID:');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_ENTRY_COMMENTS', 'System Comments: ');


define('MODULE_PAYMENT_MYPOS_VIRTUAL_PURCHASE_DESCRIPTION_TITLE', 'All the items in your shopping basket (see details in the store and on your store receipt).');
define('MODULE_PAYMENT_MYPOS_VIRTUAL_PURCHASE_DESCRIPTION_ITEMNUM', STORE_NAME . ' Purchase');
define('MODULES_PAYMENT_MYPOS_VIRTUALSTD_LINEITEM_TEXT_ONETIME_CHARGES_PREFIX', 'One-Time Charges related to ');
define('MODULES_PAYMENT_MYPOS_VIRTUALSTD_LINEITEM_TEXT_SURCHARGES_SHORT', 'Surcharges');
define('MODULES_PAYMENT_MYPOS_VIRTUALSTD_LINEITEM_TEXT_SURCHARGES_LONG', 'Handling charges and other applicable fees');
define('MODULES_PAYMENT_MYPOS_VIRTUALSTD_LINEITEM_TEXT_DISCOUNTS_SHORT', 'Discounts');
define('MODULES_PAYMENT_MYPOS_VIRTUALSTD_LINEITEM_TEXT_DISCOUNTS_LONG', 'Credits applied, including discount coupons, gift certificates, etc');
