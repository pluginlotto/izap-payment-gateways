<?php
/**************************************************
* PluginLotto.com                 *
* Copyrights (c) 2005-2010. iZAP         *
* All rights reserved               *
***************************************************
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* @version 1.0
* Under this agreement, No one has rights to sell this script further.
* For more information. Contact "Tarun Jangra<tarun@izap.in>"
* For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
* Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */


//Setting information about the transaction
$params['receivedSecurityCode'] = urldecode($_POST['ap_securitycode']);
$params['receivedMerchantEmailAddress'] = urldecode($_POST['ap_merchant']);
$params['transactionStatus'] = urldecode($_POST['ap_status']);
$params['testModeStatus'] = urldecode($_POST['ap_test']);
$params['purchaseType'] = urldecode($_POST['ap_purchasetype']);
$params['totalAmountReceived'] = urldecode($_POST['ap_totalamount']);
$params['feeAmount'] = urldecode($_POST['ap_feeamount']);
$params['netAmount'] = urldecode($_POST['ap_netamount']);
$params['transactionReferenceNumber'] = urldecode($_POST['ap_referencenumber']);
$params['currency'] = urldecode($_POST['ap_currency']);
$params['transactionDate'] = urldecode($_POST['ap_transactiondate']);
$params['transactionType'] = urldecode($_POST['ap_transactiontype']);

//Setting the customer's information from the IPN post variables
$params['customerFirstName'] = urldecode($_POST['ap_custfirstname']);
$params['customerLastName'] = urldecode($_POST['ap_custlastname']);
$params['customerAddress'] = urldecode($_POST['ap_custaddress']);
$params['customerCity'] = urldecode($_POST['ap_custcity']);
$params['customerState'] = urldecode($_POST['ap_custstate']);
$params['customerCountry'] = urldecode($_POST['ap_custcountry']);
$params['customerZipCode'] = urldecode($_POST['ap_custzip']);
$params['customerEmailAddress'] = urldecode($_POST['ap_custemailaddress']);

//Setting information about the purchased item from the IPN post variables
$params['myItemName'] = urldecode($_POST['ap_itemname']);
$params['myItemCode'] = urldecode($_POST['ap_itemcode']);
$params['myItemDescription'] = urldecode($_POST['ap_description']);
$params['myItemQuantity'] = urldecode($_POST['ap_quantity']);
$params['myItemAmount'] = urldecode($_POST['ap_amount']);

//Setting extra information about the purchased item from the IPN post variables
$params['additionalCharges'] = urldecode($_POST['ap_additionalcharges']);
$params['shippingCharges'] = urldecode($_POST['ap_shippingcharges']);
$params['taxAmount'] = urldecode($_POST['ap_taxamount']);
$params['discountAmount'] = urldecode($_POST['ap_discountamount']);

//Setting your customs fields received from the IPN post variables
$params['USER_GUID'] = urldecode($_POST['apc_1']);
$params['TOTAL_PRODUCTS'] = urldecode($_POST['apc_2']);
$params['myCustomField_3'] = urldecode($_POST['apc_3']);
$params['myCustomField_4'] = urldecode($_POST['apc_4']);
$params['myCustomField_5'] = urldecode($_POST['apc_5']);
$params['myCustomField_6'] = urldecode($_POST['apc_6']);

$params['user'] = get_user($params['USER_GUID']);
if($user) {
  define("IPN_SECURITY_CODE", get_plugin_usersetting('alertpay_IPN_security_code', $user->guid, GLOBAL_IZAP_PAYMENT_PLUGIN));
  define("MY_MERCHANT_EMAIL", get_plugin_usersetting('alertpay_user_id', $user->guid, GLOBAL_IZAP_PAYMENT_PLUGIN));
}

if ($params['receivedMerchantEmailAddress'] == MY_MERCHANT_EMAIL 
        && $params['receivedSecurityCode'] == IPN_SECURITY_CODE
        && $params['transactionStatus'] == "Success") {
  $params['IT_WORKED'] = 'YES';
  trigger_plugin_hook('izap_payment_gateway', 'IPN_NOTIFY_ALERTPAY:SUCCESS', $params);
} else {
  $params['IT_WORKED'] = 'NO';
  trigger_plugin_hook('izap_payment_gateway', 'IPN_NOTIFY_ALERTPAY:FAIL', $params);
}

// just some test data
if ($params['testModeStatus'] == "1") {
  $params['IPN_SECURITY_CODE'] = IPN_SECURITY_CODE;
  $params['MY_MERCHANT_EMAIL'] = MY_MERCHANT_EMAIL;

  foreach($params as $key => $val) {
    $string .= $key .' = ' . $val . "<br />\n\r";
  }
  func_send_mail_byizap(array(
          'msg' => $string,
          'subject' => 'TEST MAIL FOR: ALERT PAY',
          'from' => 'testAP@izap.in',
          'from_username' => 'ALERT PAY TEST',
          'to' => 'chetan@izap.in',
  ));
}
// test data ends