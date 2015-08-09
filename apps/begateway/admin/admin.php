<?php
defined('SITEBILL_DOCUMENT_ROOT') or die('Restricted access');
/**
 * beGateway admin backend
 * @author http://ecomcharge.com
 * @version 1.0.0
 */
class begateway_admin extends Object_Manager {
  /**
   * Constructor
   */
  function __construct() {
    $this->app_title = 'beGateway';
    $this->action = 'begateway';

    $this->SiteBill();
    Multilanguage::appendAppDictionary('begateway');

    require_once (SITEBILL_DOCUMENT_ROOT.'/apps/config/admin/admin.php');
    $config_admin = new config_admin();

    if ( !$config_admin->check_config_item('apps.begateway.enable') ) {
      $config_admin->addParamToConfig('apps.begateway.enable','0',Multilanguage::_('APPLICATION_ENABLE','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.description') ) {
      $config_admin->addParamToConfig('apps.begateway.description','',Multilanguage::_('DESCRIPTION','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.description_en') ) {
      $config_admin->addParamToConfig('apps.begateway.description_en','',Multilanguage::_('DESCRIPTION_EN','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.domain_gateway') ) {
      $config_admin->addParamToConfig('apps.begateway.domain_gateway','',Multilanguage::_('DOMAIN_GATEWAY','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.domain_checkout') ) {
      $config_admin->addParamToConfig('apps.begateway.domain_checkout','',Multilanguage::_('DOMAIN_CHECKOUT','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.site_id') ) {
      $config_admin->addParamToConfig('apps.begateway.site_id','',Multilanguage::_('SITE_ID','begateway'));
    }
    if ( !$config_admin->check_config_item('apps.begateway.site_key') ) {
      $config_admin->addParamToConfig('apps.begateway.site_key','',Multilanguage::_('SITE_KEY','begateway'));
    }

    if($this->getConfigValue('apps.begateway.enable')==1){

      $this->domain_gateway = $this->getConfigValue('apps.begateway.domain_gateway');
      $this->domain_checkout = $this->getConfigValue('apps.begateway.domain_checkout');
      $this->site_id = (int)$this->getConfigValue('apps.begateway.site_id');
      $this->site_key = $this->getConfigValue('apps.begateway.site_key');
      $this->description['ru'] = $this->getConfigValue('apps.begateway.description');
      $this->description['en'] = $this->getConfigValue('apps.begateway.description_en');
    }
  }

  function main(){
    $rs .= $this->get_app_title_bar();
    $rs .= Multilanguage::_('APPLICATION_NAME','begateway').' ';
    if($this->getConfigValue('apps.begateway.enable')) {
      $rs .= Multilanguage::_('APP_ON','begateway');
    } else {
      $rs .= Multilanguage::_('APP_OFF','begateway');
    }
    return $rs;
  }
}
