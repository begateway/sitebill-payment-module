<?php
/**
 * beGateway class
 */
class begateway_site extends begateway_admin {

  function get_pay_button($bill_id, $bill_sum, $bill_payment_sum) {

    if($bill_payment_sum==''){
      $payment_amount=$bill_sum;
    }else{
      $payment_amount=$bill_payment_sum;
    }

    $user_id=(int)$this->getSessionUserId();
    $language = Multilanguage::get_current_language();

    require_once SITEBILL_DOCUMENT_ROOT . '/apps/begateway/lib/begateway-api-php/lib/beGateway.php';
    require_once SITEBILL_DOCUMENT_ROOT . '/apps/system/lib/system/user/user.php';

    require_once SITEBILL_DOCUMENT_ROOT . '/apps/begateway/classes/currency.php';
    $currency = new begateway_currency();
    $order_currency = $currency->getCurrencyCode(CURRENT_CURRENCY);

    if ($order_currency == 'RUR') {
      $order_currency = 'RUB';
    }

    $user = new User_Object;

    \beGateway\Settings::$shopId = $this->site_id;
    \beGateway\Settings::$shopKey = $this->site_key;
    \beGateway\Settings::$gatewayBase = 'https://' . $this->domain_gateway;
    \beGateway\Settings::$checkoutBase = 'https://' . $this->domain_checkout;
    #\beGateway\Logger::getInstance()->setLogLevel(\beGateway\Logger::DEBUG);

    $transaction = new \beGateway\GetPaymentPageToken;

    $transaction->money->setCurrency($order_currency);
    $transaction->money->setAmount($payment_amount);

    $transaction->setDescription(sprintf(Multilanguage::_('ORDER_DESCRIPTION', 'begateway'), $bill_id));
    $transaction->setTrackingId($user_id . '|' . $bill_id);
    $transaction->setLanguage($language);

    $sitebill_host = $this->_protocol_scheme() . '://' . $_SERVER['HTTP_HOST'] . SITEBILL_MAIN_URL;

    $notification_url = $sitebill_host . '/apps/begateway/listener.php';
    $notification_url = str_replace('carts.local', 'webhook.begateway.com:8443', $notification_url);
    $transaction->setNotificationUrl($notification_url);

    $transaction->setSuccessUrl($sitebill_host . '/account/balance');
    $transaction->setDeclineUrl($sitebill_host . '/account/balance/?do=add_bill');
    $transaction->setFailUrl($sitebill_host . '/account/balance/?do=add_bill');
    $transaction->setCancelUrl($sitebill_host . '/account/balance');

    $transaction->customer->setEmail($user->getEmail($user_id));
    $transaction->setAddressHidden();

    $response = $transaction->submit();

    if ($response->isSuccess()) {
      $payment_params=array();
      $payment_params['token'] = $response->getToken();
      $payment_params['url'] = \beGateway\Settings::$checkoutBase . '/checkout';

      $this->template->assign('payment_text', sprintf(Multilanguage::_('YOU_HAVE_ORDER','begateway'),(string)$payment_amount, $this->getConfigValue('ue_name')) );
      $this->template->assign('payment_button', Multilanguage::_('PAYMENT_BUTTON','begateway'));
      $this->template->assign('payment_description', $this->description[$language]);
      $this->template->assign('payment_params', $payment_params);
    } else {
      $this->template->assign('payment_error', Multilanguage::_('PAYMENT_ERROR','system') . '<br>' . $response->getMessage());
    }

    return $this->template->fetch(SITEBILL_DOCUMENT_ROOT.'/apps/begateway/site/template/pay_form.tpl');
  }

  function activateBill ( $bill_id ) {
    $user_id=0;
    $DBC=DBC::getInstance();
    $query = 'SELECT * FROM '.DB_PREFIX.'_bill WHERE bill_id=? LIMIT 1';
    $stmt=$DBC->query($query, array($bill_id));
    if($stmt){
      $ar=$DBC->fetch($stmt);
      $user_id=$ar['user_id'];
    $bill_info=$ar;
    }

    $payment_type='recharge';

    if(isset($bill_info['payment_type']) && $bill_info['payment_type']!=''){
      $payment_type=$bill_info['payment_type'];
    }

    switch($payment_type){
      case 'buy_tariff' : {
        if($bill_info['payment_params']!=''){
          $tariff_params=unserialize($bill_info['payment_params']);
        }else{
          $tariff_params=array();
        }

        if(isset($tariff_params['tariff_id']) && 0!=(int)$tariff_params['tariff_id']){
          require_once SITEBILL_DOCUMENT_ROOT.'/apps/billing/admin/admin.php';
            $BA=new billing_admin();
            $BA->setTariffToUser((int)$tariff_params['tariff_id'], $user_id);
          $query = 'UPDATE '.DB_PREFIX.'_bill SET status=1 WHERE bill_id=?';
            $stmt=$DBC->query($query, array($bill_id));
        }
        break;
      }
      case 'accesskey_buy' : {
        require_once SITEBILL_DOCUMENT_ROOT.'/apps/watchlistmanager/admin/admin.php';
        $WLM=new watchlistmanager_admin();
        $WLM->activateWatchlist($bill_id);
        $query = 'UPDATE '.DB_PREFIX.'_bill SET status=1 WHERE bill_id=?';
          $stmt=$DBC->query($query, array($bill_id));
          break;
      }
      default : {
        if($user_id!=0){
          $OutSum=$bill_info['sum'];
            $account_value = $this->_getAccountValue( $user_id );
            $account_value += $OutSum;
            
            //set new account value
            $query = 'UPDATE '.DB_PREFIX.'_user SET account=? WHERE user_id=?';
            $stmt=$DBC->query($query, array($account_value, $user_id));
            
            //set status
            $query = 'UPDATE '.DB_PREFIX.'_bill SET status=1 WHERE bill_id=?';
            $stmt=$DBC->query($query, array($bill_id));
        }
      }
    }
  }

  /**
   * Check bill info
   * @param int $bill_id bill id
   * @return boolean
   */
  function checkBill ( $bill_id ) {
    $status=0;
    $DBC=DBC::getInstance();
    $query = 'SELECT `status` FROM '.DB_PREFIX.'_bill WHERE `bill_id`=? LIMIT 1';
    $stmt=$DBC->query($query, array($bill_id));
    if($stmt){
      $ar=$DBC->fetch($stmt);
      $status=$ar['status'];
    }
    if ( $status != 0 ) {
      $this->RiseError(Multilanguage::_('ORDER_PAYED_NOW','system'));
      return false;
    }
    return true;
  }

  protected function _getAccountValue( $user_id ) {
    $account=0;
    $DBC=DBC::getInstance();
    $query = 'SELECT account FROM '.DB_PREFIX.'_user WHERE user_id=? LIMIT 1';
    $stmt=$DBC->query($query, array((int)$user_id));
    if($stmt){
      $ar=$DBC->fetch($stmt);
      $account=$ar['account'];
    }
    return $account;
  }

  protected function _protocol_scheme() {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
      return 'https';
    } else {
      return 'http';
    }
  }

  function frontend () {
    if ( !$this->getConfigValue('apps.begateway.enable') ) {
      return false;
    }
  }
}
?>
