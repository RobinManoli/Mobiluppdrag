<?php
require('init.php');
$content = '';

if ($auth > 0){
//  if (isset($get['saved'])) $content .= '<p>Information sparad.</p>';
  if (isset($get['pwerr'])) $content .= 'Fel! Lösenordet ändrades ej: det gamla lösenordet skrevs in fel.';
  else if (isset($get['pwmismatch'])) $content .= 'Fel! Lösenordet ändrades ej: lösenordsfälten innehöll olika lösenord. Försök gärna igen.';
  $display_items = array('name', 'mobile_number');
  $editable_items = array('name', 'mobile_number', 'email', 'password');
  $content .= get_editable_bean_div($users_table, $userbean, 'e', $display_items, $editable_items, false);
  template('Inställningar', $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
