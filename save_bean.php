<?php
require('init.php');
if ($auth < 1){
  header( 'Location: ./' ) ;
}
else{
  $table = $post['table'];
  unset($post['table']);
  unset($post['submit']);
  //print_r($post);
  
  // keep track of creating new table, to make sure phone numbers are stored as strings (to keep leading zeroes)
  if (!R::find($table)) $initiate_table = true;
  else $initiate_table = false;
  // create new
  if (!isset($post['id'])){
    $bean = R::dispense($table);
    $bean->owner = $owner;
    $bean->removed = 0;
    $edit_access = true;
  }
  // update
  else {
    $bean = R::load($table, $post['id']);
    if ($bean->owner == $owner) $edit_access = true;
    else $edit_access = false;
  }

  if ($edit_access){
    // new email
    if (isset($post['email']) && $post['id'] == $userbean->id) $_SESSION['email'] = $post['email'];
    // don't attempt to update id, but need to know it until this above line at least
    unset($post['id']);
    
    // new password
    $pwerr = '';
    $pwmismatch = '';
    if (isset($post['old_password']) && isset($post['new_password']) && isset($post['repeat_password'])){
      if ($post['old_password'] == '' && $post['new_password'] == '' && $post['repeat_password'] == '') {}
      else if (sha1($post['old_password']) != $session['password']) $pwerr = '&pwerr';
      else if ($post['new_password'] == $post['repeat_password']){
        $bean->password = sha1($post['new_password']);
        $_SESSION['password'] = $bean->password; 
      }
      else $pwmismatch = '&pwmismatch';
    }
    if (isset($post['new_password'])) unset($post['new_password']);
    if (isset($post['old_password'])) unset($post['old_password']);
    if (isset($post['repeat_password'])) unset($post['repeat_password']);
    
    // save
    $bean->import($post);
    if ($initiate_table) {
      foreach($post as $attr=>$value) {
        // make sure all attributes are strings in database to keep leading zeroes
        $bean->$attr .= 's';
      }
      R::store($bean);
      foreach($post as $attr=>$value) {
        // make sure all attributes are strings in database to keep leading 0s
        $bean->$attr = substr($bean->$attr, 0, -1);
      }
    }
    R::store($bean);
    $id = $bean->id;
  }
  
  //echo "<a href='" .$server['HTTP_REFERER']. "'>Tillbaks</a>";
  // remove $get vars from referer url
  header("location: $backlink?saved&id=$id$pwerr$pwmismatch");
  //header("location:$referer?saved$pwmismatch");
}  
?>
