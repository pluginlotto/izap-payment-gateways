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

$gateway = func_get_custom_value_byizap(array(
        'plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN,
        'var' => 'installed_gateways',
));

$form = '<fieldset class="payment_fieldset">';
$form .= '<legend>'.elgg_echo('izap_payment:choose_multiple').'</legend>';
$form .= elgg_view('input/checkboxes', array(
        'internalname' => 'params[gateway_1]',
        'options' => $gateway['multi'],
        'value' => explode('|', get_plugin_usersetting('gateway_1', elgg_get_logged_in_user_guid(), GLOBAL_IZAP_PAYMENT_PLUGIN)),
));
$form .= '</fieldset><br />';

$form .= '<fieldset class="payment_fieldset">';
$form .= '<legend>'.elgg_echo('izap_payment:choose_single').'</legend>';
$form .= elgg_view('input/radio', array(
        'internalname' => 'params[gateway_2]',
        'options' => $gateway['single'],
        'value' => get_plugin_usersetting('gateway_2', elgg_get_logged_in_user_guid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
));
$form .= '</fieldset><br />';

//$form .= '<fieldset class="payment_fieldset">';
//$form .= '<legend>'.elgg_echo('izap_payment:bypass_payment').'</legend>';
//$form .= elgg_view('input/checkboxes', array(
//        'internalname' => 'params[bypass_payment]',
//        'options' => array(
//          'yes'
//        ),
//        'value' => get_plugin_usersetting('bypass_payment', get_loggedin_userid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
//));
//$form .= '</fieldset>';

$form .= elgg_view('input/hidden', array(
        'internalname' => 'params[plugin_name]',
        'value' => GLOBAL_IZAP_PAYMENT_PLUGIN,
));

$form .= elgg_view('input/hidden', array(
        'internalname' => 'params[default_values]',
        'value' => serialize(array(
        'gateway_1' => 'none',
        )),
));

$form .= elgg_view('input/submit', array(
        'value' => elgg_echo('izap_payment:submit'),
));

$form = elgg_view('input/form', array(
        'body' => $form,
        'action' => IzapBase::getFormAction('choose_gateway',GLOBAL_IZAP_PAYMENT_PLUGIN
                )));
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
  $form = izap_elgg_bridge_view('tabs',array('tabsArray' => $tab_array));
  ?>
<div class="contentWrapper">
    <?php echo $form;?>
</div>
  <?php
}
?>