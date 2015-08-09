<?php
session_start();
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors','Off');


require_once(__DIR__ . '/lib/begateway-api-php/lib/beGateway.php');
require_once("../../inc/db.inc.php");

$settings=parse_ini_file('../../settings.ini.php',true);
if(isset($settings['Settings']['estate_folder']) AND ($settings['Settings']['estate_folder']!='')){
  $folder='/'.$settings['Settings']['estate_folder'];
}else{
  $folder='';
}
$sitebill_document_root = $_SERVER['DOCUMENT_ROOT'].$folder;

define('SITEBILL_DOCUMENT_ROOT', $sitebill_document_root);
define('SITEBILL_MAIN_URL', $folder);
define('DB_PREFIX', $__db_prefix);

require_once(SITEBILL_DOCUMENT_ROOT.'/third/smarty/Smarty.class.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/init.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/db/MySQL.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/sitebill.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/admin/object_manager.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/multilanguage/multilanguage.class.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/begateway/admin/admin.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/begateway/site/site.php');

$smarty = new Smarty;
$smarty->cache_dir    = SITEBILL_DOCUMENT_ROOT.'/cache/smarty';
$smarty->compile_dir  = SITEBILL_DOCUMENT_ROOT.'/cache/compile';

$S=new SiteBill();
$begateway_site = new begateway_site();

if(0==(int)$S->getConfigValue('apps.begateway.enable')){
  echo "ERROR 01";
  exit();
}
\beGateway\Settings::$shopId = $begateway_site->site_id;
\beGateway\Settings::$shopKey = $begateway_site->site_key;

$webhook = new \beGateway\Webhook;

if ( !$webhook->isAuthorized() || !$webhook->isSuccess() ) {
  //$S->writeLog(array('apps_name'=>'apps.', 'method' => __METHOD__, 'message' => 'Подпись не совпадает', 'type' => NOTICE));
  echo "ERROR 02";
  exit();
}

list($user_id,$bill_id) = explode('|',$webhook->getTrackingId());

if($begateway_site->checkBill($bill_id)){
  $begateway_site->activateBill($bill_id);
  echo "OK";
} else {
  echo "ERROR 00";
}

exit();
?>
