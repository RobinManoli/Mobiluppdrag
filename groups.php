<?php
require('init.php');
$content = '';

if ($auth > 0){
  $display_items = array('name');
  $editable_items = array('name');
  $content .= get_editable_bean_list($groups_table, '1 ORDER BY name', $display_items, $editable_items);
  template('Telefonbok', $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
