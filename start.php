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

//global definitions
define('GLOBAL_IZAP_PAYMENT_PLUGIN', 'izap-payment-gateways');
define('GLOBAL_IZAP_PAYMENT_PAGEHANDLER', 'payment');
define('GLOBAL_IZAP_PAYMENT_SUBTYPE', 'izap_payments');
define('GLOBAL_IZAP_PAYMENT_ACTION', 'izap_payments');

function izap_payments_init() {
  global $CONFIG, $IZAP_PAYMENT_GATEWAYS;

  if(elgg_is_active_plugin('izap-elgg-bridge')) {
    izap_plugin_init(GLOBAL_IZAP_PAYMENT_PLUGIN);
  }else {
    register_error(GLOBAL_IZAP_ECOMMERCE_PLUGIN . ' plugin, needs izap-elgg-bridge');
    disable_plugin(GLOBAL_IZAP_ECOMMERCE_PLUGIN);
  }

  include_once(dirname(__FILE__) . '/lib/interfaces/paymentGateways.php');
  include_once(dirname(__FILE__) . '/lib/functions/core.php');

  $actions_arr = array(
      'admin'=>array(
          GLOBAL_IZAP_PAYMENT_ACTION . '/choose_gateway' => "choose_gateway.php"
      ) );
  foreach($actions_arr as $access_id => $actions) {
    foreach($actions as $action=>$filename) {
      elgg_register_action($action, $CONFIG->pluginspath. GLOBAL_IZAP_PAYMENT_PLUGIN.'/actions/' . $filename, $access_id );
      elgg_register_plugin_hook_handler('action', $action, GLOBAL_IZAP_ACTIONHOOK);
    }
  }

  //registeration of submenu
if (in_array(get_context(), array('settings', 'notifications', 'payment'))) {
    $submenu = array(
                'pg/'.GLOBAL_IZAP_PAYMENT_PAGEHANDLER.'/choose_gateway/'.get_loggedin_user()->username.'/'=>array('title'=>"izap_payment:choose_gateway", 'admin_only'=>true, 'groupby' => 'all'),
              );
    foreach($submenu as $url=>$options) {
      if( isset($options['public']) && $options['public']==TRUE && !elgg_is_logged_in() ) {
        continue;
      } else if( isset($options['admin_only']) && $options['admin_only']==true && !elgg_is_admin_logged_in() ) {
        continue;
      } else {
        elgg_register_menu_item('page', array(
          'name'=>elgg_echo($options['title']),
          'text'=>elgg_echo($options['title']),
          'href'=>$url,
          'section'=>$options['groupby']
        ));
      }
    }
  }

  elgg_register_page_handler(GLOBAL_IZAP_PAYMENT_PAGEHANDLER, GLOBAL_IZAP_PAGEHANDLER);
  elgg_register_plugin_hook_handler('alertpay', 'alert_url', 'izap_alertpay_alert_url');
  //gateways available
  $IZAP_PAYMENT_GATEWAYS->custom = array(
    'installed_gateways' => array(
      'multi' => array('paypal', 'alertpay'),
      'single' => array('payleap', 'authorize', 'none'),
    ),
    'gateways_info' => array(
      'paypal' => array(
        'title' => 'Paypal',
      ),
      'payleap' => array(
        'title' => 'Credit card',
      ),
      'authorize' => array(
        'title' => 'Credit card',
      ),
      'alertpay' => array(
        'title' => 'Alert pay'
      ),
    )
  );
}
elgg_register_event_handler('init', 'system', 'izap_payments_init');

// Functions from izap-elgg-bridge 1.7
function func_array_to_plugin_settings($value) {
  if(!is_array($value)) {
    return $value;
  }

  if(count($value) == 1) {
    $new_value = current($value);
  }else {
    $new_value = implode('|', $value);
  }

  return $new_value;
}
function func_get_admin_entities_byizap($array = array()) {
  global $CONFIG;

  $options = array(
          'type' => 'user',
          'joins' => array(
                  ' JOIN ' . $CONFIG->dbprefix . 'users_entity u ON u.guid = e.guid'
          ),
          'wheres' => array(
                  '(u.admin = "yes")'
          ),
  );

  $options = array_merge($options, $array);
  return elgg_get_entities($options);
}
