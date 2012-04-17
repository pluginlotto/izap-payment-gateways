<?php

/* * ************************************************
 * PluginLotto.com                                 *
 * Copyrights (c) 2005-2010. iZAP                  *
 * All rights reserved                             *
 * **************************************************
 * @author iZAP Team "<support@izap.in>"
 * @link http://www.izap.in/
 * @version {version} $Revision: {revision}
 * Under this agreement, No one has rights to sell this script further.
 * For more information. Contact "Tarun Jangra<tarun@izap.in>"
 * For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
 * Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */

izap_gatekeeper(array('plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN));
set_context('settings');
$title = elgg_echo('izap_payment:choose_gateway');
$body = elgg_view_layout(
        'two_column_left_sidebar', '', elgg_view_title($title) .
        func_izap_bridge_view(
                'forms/choose_gateway', array(
            'plugin' => GLOBAL_IZAP_PAYMENT_PLUGIN,
                )
        )
);
page_draw($title, $body);