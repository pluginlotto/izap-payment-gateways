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

function func_get_payment_options($user_guid = 0) {
  $gate_1 = explode('|', func_get_gateway_setting('gateway_1', $user_guid));
  $gate_2 = func_get_gateway_setting('gateway_2', $user_guid);

  if($gate_1 || $gate_2) {
  $gateways = array_merge((array) $gate_1,(array) $gate_2);
  }

  foreach($gateways as $gate) {
    if($gate && $gate != '') {
      $return[] = $gate;
    }
  }

  return $return;
}

function func_get_gateway_setting($name, $user_guid = 0) {
  if(!$user_guid) {
    $user_guid = get_loggedin_userid();
  }

  if(!$user_guid) {
    return FALSE;
  }

  return get_plugin_usersetting($name, $user_guid, GLOBAL_IZAP_PAYMENT_PLUGIN);
}

function func_izap_simple_xml_find($haystack, $needle) {
// supplying a valid closing XML tag in $needle, this will return the data contained by the element
// the element in question must be a leaf, and not itself contain other elements (this is *simple*_xml_find =)

if(($end = strpos($haystack, $needle)) === FALSE)
	return("");

for($x = $end; $x > 0; $x--) {
	if($haystack{$x} == ">")
		return(trim(substr($haystack, $x + 1, $end - $x - 1)));
	}
return ("");
}