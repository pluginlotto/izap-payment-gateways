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

class IzapPayment {

  public $gateway_path;
  private $gateway;
  private $debug_mode = FALSE;

  private $return_msg = array();

  public function __construct($method = '') {
    if(empty ($method)) {
      $method = $_POST['payment_option'];
    }
    $this->gateway_path = dirname(__FILE__) . '/gateways/';

    // check gateway
    if(empty ($method)) {
      $this->addError(elgg_echo('izap_payment:undefined_gateway'));
    }else
    // try including the gateway file
      $filename = $this->gateway_path . $method . '.php';
    if(file_exists($filename) && include_once ($filename)) {
      if(class_exists($method)) {
        $this->gateway = new $method();
      }else {
        $this->addError(elgg_echo('izap_payment:class_not_found'));
      }
    }else {
      $this->addError(elgg_echo('izap_payment:file_not_find'));
    }
  }

  public function setParams($array) {
    if($this->hasError()) {
      return FALSE;
    }

    if(empty ($array)) {
      $this->addError(elgg_echo('izap_payment:empty_param'));
      return FALSE;
    }

    if(!is_array($array)) {
      $array = array($array);
    }

    $this->gateway->setParams($array);
  }

  public function process($user_guid = 0) {
    if($this->hasError()) {
      return FALSE;
    }

    return $this->gateway->submit($user_guid);
  }

  public function validate($debug = FALSE) {
    if($this->hasError()) {
      return FALSE;
    }

    return $this->gateway->validate($debug);
  }

  public function getResponse() {
    if($this->hasError()) {
      return FALSE;
    }

    return $this->gateway->getResponse();
  }

  public function getTransactionId() {
    if($this->hasError()) {
      return FALSE;
    }

    return $this->gateway->getTransactionId();
  }

  public function hasError() {
    return (bool) $this->return_msg['ERROR'];
  }

  private function addMsg($msg) {
    if( !empty ($msg)) {
      $this->return_msg['SUCCESS'] = TRUE;
      $this->return_msg['error_msg'][] = $msg;
    }
  }

  private function addError($error) {
    if( !empty ($error)) {
      $this->return_msg['ERROR'] = TRUE;
      $this->return_msg['SUCCESS'] = FALSE;
      $this->return_msg['error_msg'][] = $error;
    }
  }

  public function settingForm() {
    global $CONFIG;
    if($this->hasError()) {
      return elgg_echo('izap_payment:method_not_supported');
    }

    $form = $this->gateway->settingForm();
    $form .= elgg_view('input/hidden', array(
            'internalname' => 'params[plugin_name]',
            'value' => GLOBAL_IZAP_PAYMENT_PLUGIN,
    ));
    $form .= elgg_view('input/submit', array(
            'value' => elgg_echo('izap_payment:submit'),
    ));
    $form = elgg_view('input/form', array(
            'body' => $form,
            'action' => $CONFIG->www . 'action/'.GLOBAL_IZAP_PAYMENT_ACTION.'/choose_gateway',
    ));
    return $form;
  }

  public function inputForm() {
    if($this->hasError()) {
      return elgg_echo('izap_payment:method_not_supported');
    }

    $form = $this->gateway->inputForm();
    
    $form .= elgg_view('input/hidden', array(
      'internalname' => 'payment_option',
      'value' => get_class($this->gateway),
    ));

    
    return $form;
  }

  private function __call($name,  $arguments) {
    return array(
            'method you are trying to call, doesn\'t exists.'
    );
  }
}
