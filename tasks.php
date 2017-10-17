<?php
require('init.php');
$content = '';

if ($auth > 0){
  $display_items = array('task', 'other_info');
//  $editable_items = array('name', 'mobile_number', 'email');
  if (isset($get['removed'])){
    $removed = 1;
    $header = "Borttagna Uppgifter";
    $submenu = array("?null"=>"Se ej borttagna");
  }
  else{
    $removed = 0;
    $header = "Uppgifter";
    $submenu = array("?removed"=>"Se borttagna");
  }
  $content .= get_editable_bean_list($tasks_table, get_all_user_tasks($removed), $display_items);
  template($header, $content, array("submenu"=>$submenu));
}

else {
  header( 'Location: ./' ) ;
}
?>
