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

$payment_options = func_get_payment_options($vars['user_guid']);
$gateway_info = func_get_custom_value_byizap(array('plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN, 'var' => 'gateways_info'));

if($payment_options) {
  ?>
<div class="choose_payment_options">
    <?php
    foreach($payment_options as $key => $gate) {
      $option_url = func_set_href_byizap(
              array(
              'plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN,
              'page' => 'load_input_form',
              'vars' => array($gate)
              )
      );
      if($key == 0) {
        $class = 'selected';
        $url = $option_url;
      }else {
        $class = '';
      }
      ?>
  <a href="<?php echo $option_url?>"
     class="payment_option_link <?php echo $class;?>"
     title="<?php echo $gateway_info[$gate]['title']?>"
     ><img src="<?php echo func_get_www_path_byizap(array(
                   'plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN,
                   'type' => 'images'
                   )) . $gate . '.gif';
      ?>" alt="<?php echo $gateway_info[$gate]['title']?>"
      /></a>
        <?php
      }
      ?>
</div>
<div id="payment_option_from"><?php echo elgg_echo('izap_payment:loading')?></div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#payment_option_from').load('<?php echo $url;?>');

    $('.payment_option_link').click(function () {
      $('.payment_option_link').removeClass('selected');
      $(this).addClass('selected');
      $('#payment_option_from').html('<?php echo elgg_echo('izap_payment:loading')?>');
      $('#payment_option_from').load(this.href);
      return false;
    });
  });
</script>
  <?php
}
?>