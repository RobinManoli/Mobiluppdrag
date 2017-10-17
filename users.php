<?php
require('init.php');
$content = '';

if ($auth > 0){
  $display_items = array('name', 'mobile_number');
  $editable_items = array('name', 'mobile_number', 'email');

  if (isset($get['removed'])){
    $removed = 1;
    $header = "Borttagna uppdragstagare";
    $submenu = array("?null"=>"Se ej borttagna");
  }
  else{
    $removed = 0;
    $header = "Uppdragstagare";
    $submenu = array("?removed"=>"Se borttagna");
  }

  $user_buttons = array('user_assignments.php?id=$id'=>'Visa uppdragslista för $name');
  $options = array('user_buttons'=>$user_buttons);

  $content .= get_editable_bean_list($users_table, get_all_user_users($removed), $display_items, $editable_items, $options);
  template($header, $content, array("submenu"=>$submenu));
}

else {
  header( 'Location: ./' ) ;
}
?>
