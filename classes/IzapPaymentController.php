<?php
class IzapPaymentController extends IzapController {
  protected $_page;
  public function  __construct($page) {
    parent::__construct($page);
    global $IZAP_PAYMENT_GATEWAYS;
    $this->_page = $page;
    $this->page_elements['filter']=false;
  }
  public function actionChoose_gateway() {
    admin_gatekeeper();
    echo "Sona";
    echo elgg_get_plugin_setting('global_payment_gateway');
    if (elgg_get_plugin_setting('global_payment_gateway') == 'yes') {
      register_error("Sorry this link is not authorized to access.");
      forward();
    }
    set_context('settings');
    $this->page_elements['title'] = elgg_echo('izap_payment:choose_gateway');
    $this->page_elements['content'] = elgg_view( GLOBAL_IZAP_PAYMENT_PLUGIN . '/forms/choose_gateway' );
    $this->drawPage();
  }
  public function actionLoad_input_form() {
    $payment = new IzapPayment( $this->url_vars[2] );
    echo $payment->inputForm();
  }
}
