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

class paypal implements paymentGateways {

  var $last_error;                 // holds the last error encountered

  var $ipn_log;                    // bool: log IPN results to text file?
  var $ipn_log_file;               // filename of the IPN log
  var $ipn_response;               // holds the IPN response from paypal
  var $ipn_data = array();         // array contains the POST values for IPN

  var $fields = array();           // array holds the fields to submit to paypal
  var $debug = false;

  function paypal($debug=false) {
    global $CONFIG;
    // initialization constructor.  Called when class is created.

    if($debug) {
      $this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
      $this->debug = true;
    }
    else {
      $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    }

    $this->last_error = '';

    $this->ipn_log_file = $CONFIG->pluginspath . GLOBAL_BRIDGE_PLUGIN . '/ipn_log.txt';
    $this->ipn_log = true;
    $this->ipn_response = '';
    $this->ipn_posted_vars = '';
  }

  public function testMode($test_mode = false) {
    if($test_mode) {
      $this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
      $this->debug = true;
    } else {
      $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    }
  }

  function setParams($array) {
    $default = array(
            'currency_code' => 'USD',
            'cancel_return' => $_SERVER['HTTP_REFERER'],
            'rm' => 2,
            'cmd' => '_ext-enter',
            'redirect_cmd' => '_cart',
            'upload' => 1,
    );

    $options = array_merge($default, $array);

    foreach($options as $field => $value) {
      if($field == 'items') {
        foreach($value as $key => $product) {
          $this->fields['item_number_' . $key] = $key;
          $this->fields['item_name_' . $key] = $product['name'];
          $this->fields['amount_' . $key] = $product['amount'];
        }
      }else {
        $this->fields["$field"] = $value;
      }
    }
  }

  function submit($user_guid = 0) {
    if(!$user_guid) {
      $user_guid = elgg_get_logged_in_user_guid();
    }

    if(elgg_get_plugin_user_setting('paypal_test_mode', $user_guid, GLOBAL_IZAP_PAYMENT_PLUGIN) == 'yes') {
      $mode = true;
    }
    $this->testMode($mode);
    $this->fields['business'] = elgg_get_plugin_user_setting('paypal_account', $user_guid, GLOBAL_IZAP_PAYMENT_PLUGIN);

    echo "<html>\n";
    echo "<head><title>Processing Payment...</title>";
    echo "</head>\n";
    echo "<body onLoad=\"document.form.submit();\">\n";
    echo "<p align=\"center\"><img src=\"http://cdn.iconfinder.net/data/icons/creditcarddebitcard/64/paypal-curved.png\"><br />
      <h3 align='center'>Processing... Please don't refresh or press back button.</h3>
      </p>";
    echo "<form method=\"post\" name=\"form\" action=\"".$this->paypal_url."\">\n";

    foreach ($this->fields as $name => $value) {
      echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
    }

    echo "</form>\n";
    echo "</body></html>\n";

    return true;

  }

  public function validate($debug = FALSE) {
    $this->testMode($debug);
    if($this->verifyResponse()) {
      $returnArr['status'] = TRUE;
      $returnArr['ipn_posted_vars'] = $this->ipn_posted_vars;
      $returnArr['ipn_data'] = $this->ipn_data;
      $returnArr['invoiceid'] = $this->ipn_data['txn_id'];
      $returnArr['ipn_response'] = $this->ipn_response;
    }else {
      $returnArr['ipn_posted_vars'] = $this->ipn_posted_vars;
      $returnArr['ipn_response'] = $this->ipn_response;
      $returnArr['status'] = FALSE;
    }
    return $returnArr;
  }

  public function verifyResponse() {

    // parse the paypal URL
    $url_parsed=parse_url($this->paypal_url);
    $post_string = '';
    foreach ($_POST as $field=>$value) {
      $this->ipn_data["$field"] = $value;
      $post_string .= $field.'='.urlencode($value).'&';
    }
    $post_string.="cmd=_notify-validate"; // append ipn command

    // open the connection to paypal
    $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30);
    if(!$fp) {

      // could not open the connection.  If loggin is on, the error message
      // will be in the log.
      $this->last_error = "fsockopen error no. $errnum: $errstr";
      $this->logIpnResults(false);
      return false;

    } else {

      // Post the data back to paypal
      fputs($fp, "POST $url_parsed[path] HTTP/1.1\n");
      fputs($fp, "Host: $url_parsed[host]\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
      fputs($fp, "Content-length: ".strlen($post_string)."\n");
      fputs($fp, "Connection: close\n\n");
      fputs($fp, $post_string . "\n\n");

      // loop through the response from the server and append to variable
      while(!feof($fp)) {
        $this->ipn_response .= fgets($fp, 1024);
      }

      fclose($fp); // close connection
    }

    if (eregi("VERIFIED",$this->ipn_response)) {

      // Valid IPN transaction.
      $this->logIpnResults(true);
      return true;

    } else {

      // Invalid IPN transaction.  Check the log for details.
      $this->last_error = 'IPN Validation Failed.';
      $this->logIpnResults(false);
      return false;

    }

  }

  function logIpnResults($success) {

    if (!$this->ipn_log) return;  // is logging turned off?

    // Timestamp
    $text = '['.date('m/d/Y g:i A').'] - ';

    // Success or failure being logged?
    if ($success) $text .= "SUCCESS!\n";
    else $text .= 'FAIL: '.$this->last_error."\n";

    // Log the POST variables
    $text .= "IPN POST Vars from Paypal:\n";


    foreach ($this->ipn_data as $key=>$value) {
      $text .= "$key=$value, ";
      $this->ipn_posted_vars .= $key.'='.$value."<br>";
    }

    // Log the response from the paypal server
    $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;

    // Write to log
    $fp=fopen($this->ipn_log_file,'a');
    fwrite($fp, $text . "<hr \>\n\n");

    fclose($fp);  // close file
  }

  function dumpFields() {

    echo "<h3>paypal_class->dump_fields() Output:</h3>";
    echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";

    ksort($this->fields);
    foreach ($this->fields as $key => $value) {
      echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
    }

    echo "</table><br>";
  }

  public function __call($name,  $arguments) {
    return array(
            'method you are trying to call, doesn\'t exists.'
    );
  }

  public function settingForm() {
    $form = '<label>' . elgg_echo('izap_payment:paypal_account');
    $form .= elgg_view('input/text',
            array(
            'name' => 'params[paypal_account]',
            'value' => elgg_get_plugin_user_setting('paypal_account', elgg_get_logged_in_user_guid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            )
    );
    $form .= '</label>';

    $form .= '<br />';

    $form .= '<label>' . elgg_echo('izap_payment:test_mode') . '<br />';
    $form .= elgg_view('input/radio',
            array(
            'name' => 'params[paypal_test_mode]',
            'value' => elgg_get_plugin_user_setting('paypal_test_mode', elgg_get_logged_in_user_guid(), GLOBAL_IZAP_PAYMENT_PLUGIN),
            'class' => 'general-text',
            'options' => array(
                    elgg_echo('izap_payment:yes') => 'yes',
                    elgg_echo('izap_payment:no') => 'no',
            ),
            )
    );
    $form .= '</label>';
    $form .= '<div class="gateway_help"><a href="#" onclick="$(\'#help_div_paypal\').toggle(); return false;">'.elgg_echo('izap_payment:help').'</a>';
    $form .= '<div style="display: none;" id="help_div_paypal">
      Add you paypal account and select the payment mode.
              </div></div>';
    return $form;
  }

  public function inputForm() {
    $form = '';

    return $form;
  }

  public function getTransactionId() {
    return '';
  }
  public function getResponse() {
    return '';
  }
}