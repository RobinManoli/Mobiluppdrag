<?php
require('init.php');
$content = '';

if ($auth > 0){
  $content .= get_assignments_list();
  template('Uppdrag', $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
