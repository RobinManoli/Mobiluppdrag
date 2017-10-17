<?php
require('init.php');
$content = '';

if ($auth > 0){
  $display_items = array('location', 'other_info');
//  $editable_items = array('location', 'other_info');
  if (isset($get['removed'])){
    $removed = 1;
    $header = "Borttagna platser";
    $submenu = array("?null"=>"Se ej borttagna");
  }
  else{
    $removed = 0;
    $header = "Platser";
    $submenu = array("?removed"=>"Se borttagna");
  }

  $content .= get_editable_bean_list($locations_table, get_all_user_locations($removed), $display_items);
  template($header, $content, array("submenu"=>$submenu));
}

else {
  header( 'Location: ./' ) ;
}
?>
