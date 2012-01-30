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
global $IZAP_PAYMENT_GATEWAYS;
$gateway = $IZAP_PAYMENT_GATEWAYS->custom['installed_gateways'];

$form = '<fieldset class="payment_fieldset">';
$form .= '<legend>'.elgg_echo('izap_payment:choose_multiple').'</legend>';
$form .= elgg_view('input/checkboxes', array(
        'name' => 'params[gateway_1]',
        'options' => $gateway['multi'],
        'value' => explode('|', get_plugin_usersetting('gateway_1', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN)),
));
$form .= '</fieldset><br />';

$form .= '<fieldset class="payment_fieldset">';
$form .= '<legend>'.elgg_echo('izap_payment:choose_single').'</legend>';
$form .= elgg_view('input/radio', array(
        'name' => 'params[gateway_2]',
        'options' => $gateway['single'],
        'value' => get_plugin_usersetting('gateway_2', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
));
$form .= '</fieldset><br />';

//$form .= '<fieldset class="payment_fieldset">';
//$form .= '<legend>'.elgg_echo('izap_payment:bypass_payment').'</legend>';
//$form .= elgg_view('input/checkboxes', array(
//        'name' => 'params[bypass_payment]',
//        'options' => array(
//          'yes'
//        ),
//        'value' => get_plugin_usersetting('bypass_payment', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
//));
//$form .= '</fieldset>';

$form .= elgg_view('input/hidden', array(
        'name' => 'params[plugin_name]',
        'value' => GLOBAL_IZAP_PAYMENT_PLUGIN,
));

$form .= elgg_view('input/hidden', array(
        'name' => 'params[default_values]',
        'value' => serialize(array(
        'gateway_1' => 'none',
        )),
));

$form .= elgg_view('input/submit', array(
        'value' => elgg_echo('izap_payment:submit'),
));

$form = elgg_view('input/form', array(
        'body' => $form,
        'action' => $vars['url'] . 'action/'.GLOBAL_IZAP_PAYMENT_ACTION.'/choose_gateway',
));
?>
<div class="contentWrapper">
  <?php echo $form;?>
</div>
<?php
unset ($form);
$gateway = func_get_payment_options();
if($gateway) {
  echo elgg_view_title(elgg_echo('izap_payment:gateways_settings'));
  foreach($gateway as $gate) {
    $payment_gate = new IzapPayment($gate);
    $tab_array[] = array(
            'title' => $gate,
            'content' => $payment_gate->settingForm(),
    );
  }
  $form = elgg_view(GLOBAL_IZAP_ELGG_BRIDGE.'/views/tabs',array('tabsArray' => $tab_array));
  ?>
<div class="contentWrapper">
    <?php echo $form;?>
</div>
  <?php
}
?>