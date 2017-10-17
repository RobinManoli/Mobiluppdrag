<?php
require('init.php');
$content = '';

if ($auth > 0){
  $header = 'Uppdragslista';
  $content .= get_user_assignments_list($get['id']);

  printable_template($header, $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
