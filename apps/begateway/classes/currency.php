<?php
require_once SITEBILL_DOCUMENT_ROOT . '/apps/currency/admin/admin.php';

class begateway_currency extends currency_admin {

  public function getCurrencyCode($currency_id) {
    $query='SELECT code FROM '.DB_PREFIX.'_'.$this->table_name.' WHERE '.$this->primary_key.' = '.(int)$currency_id . ' LIMIT 1';
    $this->db->exec($query);
    if($this->db->success){
      while($this->db->fetch_assoc()){
        return $this->db->row['code'];
      }
    }
    return 'RUR';
  }
}
?>
