<?php
/**************************************************
* PluginLotto.com                                 *
* Copyrights (c) 2005-2010. iZAP                  *
* All rights reserved                             *
***************************************************
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* @version 1.0
* Under this agreement, No one has rights to sell this script further.
* For more information. Contact "Tarun Jangra<tarun@izap.in>"
* For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
* Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */

class alertpay extends gateWayMethods implements paymentGateways {
  public $form_post_url;

  private $fields = array();
  private $debug;

  public function __construct() {
    $this->form_post_url = 'https://www.alertpay.com/PayProcess.aspx';

  }

  public function getResponse() {
    ;
  }

  public function getTransactionId() {
    ;
  }

  public function inputForm() {
    ;
  }

  public function setParams($array) {
    $default = array(
            'ap_purchasetype' => 'item-goods',
            'ap_cancelurl' => $_SERVER['HTTP_REFERER'],
            'ap_currency' => 'USD',
    );
    $options = array_merge($default, $array);

    foreach($options as $field => $value) {
      if($field == 'items') {
        foreach($value as $key => $product) {
          $this->fields['ap_itemname_' . $key] = $product['name'];
          $this->fields['ap_amount_' . $key] = $product['amount'];
          $this->fields['ap_quantity_' . $key] = 1;
        }
        $this->fields['apc_2'] = count($value); // total products
      }else {
        $this->fields["$field"] = $value;
      }
    }
    $this->fields['apc_3'] = $array['custom'];// order id
  }

  public function settingForm() {
    $form = '<label>' . elgg_echo('izap_payment:alertpay_user_id');
    $form .= '<br />';
    $form .= elgg_view('input/text',
            array(
            'internalname' => 'params[alertpay_user_id]',
            'value' => get_plugin_usersetting('alertpay_user_id', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            )
    );
    $form .= '</label>';

    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:alertpay_IPN_security_code');
    $form .= '<br />';
    $form .= elgg_view('input/text',
            array(
            'internalname' => 'params[alertpay_IPN_security_code]',
            'value' => get_plugin_usersetting('alertpay_IPN_security_code', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            )
    );
    $form .= '</label><br />';
    $form .= elgg_echo('izap_payment:alert_url') . ': <i>' . trigger_plugin_hook('alertpay', 'alert_url', null, 'Alert URL not set. Please contact site admnistrator.') . '</i><br />';
    return $form;
  }

  public function submit($user_guid = 0) {
    $this->fields['ap_merchant'] = get_plugin_usersetting('alertpay_user_id', $user_guid, GLOBAL_IZAP_PAYMENT_PLUGIN);
    $this->fields['apc_1'] = $user_guid; // user guid

    echo "<html>\n";
    echo "<head><title>Processing Payment...</title>";
    echo "</head>\n";
    echo "<body onLoad=\"document.form.submit();\">\n";
    echo "<p align=\"center\"><img src=\"https://www.alertpay.com/images/AlertPay_accepted_295x43_green.png\"><br />
      <h3 align='center'>Processing... Please don't refresh or press back button.</h3>
      </p>";
    echo "<form method=\"post\" name=\"form\" action=\"".$this->form_post_url."\">\n";

    foreach ($this->fields as $name => $value) {
      echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
    }

    echo "</form>\n";
    echo "</body></html>\n";

    return TRUE;
  }

  public function validate() {
    ;
  }
}


