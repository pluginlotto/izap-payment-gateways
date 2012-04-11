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

class IzapPaymentController extends IzapController {

  protected $_page;

  public function __construct($page) {
    parent::__construct($page);
    global $IZAP_PAYMENT_GATEWAYS;
    $this->_page = $page;
    $this->page_elements['filter'] = false;
  }

  public function actionChoose_gateway() {
    admin_gatekeeper();
    set_context('settings');
    $this->page_elements['title'] = elgg_echo('izap_payment:choose_gateway');
    $this->page_elements['content'] = elgg_view(GLOBAL_IZAP_PAYMENT_PLUGIN . '/forms/choose_gateway');
    $this->drawPage();
  }

  public function actionLoad_input_form() {
    $payment = new IzapPayment($this->url_vars[2]);
    echo $payment->inputForm();
  }

}
