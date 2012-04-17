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
?>

.choose_payment_options a.payment_option_link img{
border: 4px;
}

.choose_payment_options a.selected img:visited, .choose_payment_option a.selected img:link{
border:none;
border-radius: 10px;
-moz-border-radius: 10px;
-o-border-radius: 10px;
-webkit-border-radius: 10px;
float: right;
}
.choose_payment_options a.selected img:active, .choose_payment_options a.selected img:active
{
border: 2px grey solid;
background-color: #DADAD4;
border-radius: 10px;
-moz-border-radius: 10px;
-o-border-radius: 10px;
-webkit-border-radius: 10px;
}

.payment_fieldset {
border: 1px #DEDEDE solid;
padding: 10px;
}

.payment_fieldset legend {
margin-left: 10px;
font-weight: bold;
font-size: 1.3em;
color: #0054A7;
}

.gateway_help {
background-color: #DEDEDE;
border: 2px #C1C1C1 solid;
padding: 10px;
border-radius: 8px;
-moz-border-radius: 8px;
-webkit-border-radius: 8px;
}
******************* Additional CSS *****************

.payment_method_list ul {
list-style: none;
width: auto;
height: 30px;
}

.payment_method_list li {
list-style: none;
width: 100%;
height: 65px;
margin-bottom: 3px;
padding: 3px;
padding-right: 2px;
text-align: right;
border-radius: 8px;
background-color:  #eeeeee;
-moz-border-radius: 8px;
-webkit-border-radius: 8px;
-o-border-radius: 8px;
}

.payment_method_list li:hover {
background-color: #DADAD4;
height: 65px;
}


.payment_method_list li.selected {
background-color: #DADAD4;
height: 65px;
}

a.selected li {
background-color: #DADAD4;
}
a.selected li:active {
background-color: $eeeeee;
height: 65px;
}
.clear{
clear: both;
}