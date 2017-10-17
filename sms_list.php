<?php
require('init.php');
$content = '';

if ($auth > 0){
  $display_items = array('text');
//  $editable_items = array('name', 'mobile_number', 'email');
  if (isset($get['removed'])){
    $removed = 1;
    $header = "Borttagna SMS";
    $submenu = array("?null"=>"Se ej borttagna");
  }
  else{
    $removed = 0;
    $header = "SMS";
    $submenu = array("?removed"=>"Se borttagna");
  }
  $content .= get_sms_list($removed);
  template($header, $content, array("submenu"=>$submenu));
}

else {
  header( 'Location: ./' ) ;
}
?>
