<?php
/**************************************************
* PluginLotto.com                                 *
* Copyrights (c) 2005-2010. iZAP                  *
* All rights reserved                             *
***************************************************
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* @version {version} $Revision: {revision}
* Under this agreement, No one has rights to sell this script further.
* For more information. Contact "Tarun Jangra<tarun@izap.in>"
* For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
* Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */

class payleap extends gateWayMethods implements paymentGateways {
  private $payleap_url;
  private $data = array();
  private $response;
  private $raw_response;

  public function __construct() {
    $this->payleap_url = 'https://secure1.payleap.com/TransactServices.svc/ProcessCreditCard';
  }

  public function setParams($array) {
    foreach($array as $key => $val) {
      $this->data[$key] = $val;
    }
  }

  public function submit($user_guid = 0) {
    if(func_get_gateway_setting('payleap_test_mode', $user_guid) == 'yes') {
      $this->payleap_url = 'https://uat.payleap.com/TransactServices.svc/ProcessCreditCard';
      $this->setParams(array('ExtData' => '<TrainingMode>T</TrainingMode>'));
    }

    $posted_params = get_input('payleap');

    $options = array(
            'UserName' => func_get_gateway_setting('payleap_user_id', $user_guid),
            'Password' => func_get_gateway_setting('payleap_user_pass', $user_guid),
            'Amount' => $this->data['grandTotal'],
            'TransType' => 'Sale',
            'CardNum' => htmlspecialchars($posted_params['card_number']),
            'ExpDate' => htmlspecialchars($posted_params['card_exp_date']['month'] . substr($posted_params['card_exp_date']['year'], 2)),
            'CVNum' => htmlspecialchars($posted_params['card_cvv_number']),
    );
    $this->setParams($options);

    unset ($this->data['items'], $this->data['grandTotal'], $this->data['custom']);
    $this->raw_response = $this->sendRequest($this->arrayToPostString($this->data), $this->payleap_url);
    return $this->validate();
  }

  public function validate() {

    $approval = func_izap_simple_xml_find($this->raw_response, "</Result>");
    if($approval == "0") {
      $this->response['status'] = TRUE;
      $this->response['msg'] = func_izap_simple_xml_find($this->raw_response, "</Message>");
      $this->response['success_msg'] .= func_izap_simple_xml_find($this->raw_response, "</RespMSG>");
    }else {
      $this->response['status'] = FALSE;
      $this->response['msg'] = func_izap_simple_xml_find($this->raw_response, "</Message>");
      $this->response['error_msg'] .= func_izap_simple_xml_find($this->raw_response, "</RespMSG>");
    }

    $this->response['auth_code'] .= func_izap_simple_xml_find($this->raw_response, "</AuthCode>");
    $this->response['PNRef'] .= func_izap_simple_xml_find($this->raw_response, "</PNRef>");

    return $this->response;
  }

  public function getTransactionId() {
    return $this->response['PNRef'];
  }

  public function getResponse() {
    return $this->response;
  }
  
  public function inputForm() {
    $form = '<label>' . elgg_echo('izap_payment:card_type');
    $form .= '<br />';
    $form .= elgg_view('input/pulldown', array(
            'internalname' => 'payleap[card_type]',
            'options' => array(
                    'Visa',
                    'MasterCard',
                    'American Express',
                    'Discover/Novus',
                    'Diner\'s Club/Carte Blanche',
                    'Japanese Credit Bureau',
                    'enRoute',
            ),
    ));
    $form .= '</label>';
    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:card_number');
    $form .= '<br />';
    $form .= elgg_view('input/text', array(
            'internalname' => 'payleap[card_number]',
    ));
    $form .= '</label>';
    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:card_cvv');
    $form .= '<br />';
    $form .= elgg_view('input/text', array(
            'internalname' => 'payleap[card_cvv_number]',
    ));
    $form .= '</label>';
    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:exp_date');
    $form .= '</label>';
    $form .= '<br />';
    $form .= elgg_view('input/date', array(
            'internalname' => 'payleap[card_exp_date]',
            'params' => array(
                    'start_year' => date(Y),
            ),
    ));
    $form .= '<br />';

    return $form;
  }

  public function settingForm() {
    $form = '<label>' . elgg_echo('izap_payment:payleap_user_id');
    $form .= '<br />';
    $form .= elgg_view('input/text',
            array(
            'internalname' => 'params[payleap_user_id]',
            'value' => get_plugin_usersetting('payleap_user_id', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            )
    );
    $form .= '</label>';

    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:payleap_user_pass');
    $form .= '<br />';
    $form .= elgg_view('input/text',
            array(
            'internalname' => 'params[payleap_user_pass]',
            'value' => get_plugin_usersetting('payleap_user_pass', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            )
    );
    $form .= '</label>';

    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:test_mode') . '<br />';
    $form .= elgg_view('input/radio',
            array(
            'internalname' => 'params[payleap_test_mode]',
            'value' => get_plugin_usersetting('payleap_test_mode', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            'options' => array(
                    elgg_echo('izap_payment:yes') => 'yes',
                    elgg_echo('izap_payment:no') => 'no',
            ),
            )
    );
    $form .= '</label>';
    $form .= '<div class="gateway_help"><a href="#" onclick="$(\'#help_div_payleap\').toggle(); return false;">'.elgg_echo('izap_payment:help').'</a>';
    $form .= '<div style="display: none;" id="help_div_payleap">
      Add your login id and password, and you are ready to go.
              </div></div>';
    return $form;

  }
}