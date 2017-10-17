<?php
require('init.php');
$content = '';

if ($auth > 0){
  // display confirm dlg
  if (!isset($get['delete'])){
    $table = $get['table'];
    $id = $get['id'];
    $text = $get['text'];
    $text_encoded = urlencode($text);
    $backlink_encoded = urlencode($backlink);
    $header = "Ta bort inlägg ($text)?";
    $content.= '<div class="ui-bar ui-bar-a"><h1>' .$header. '</h1></div>'; 
    $content .= '<a href="' ."?delete&amp;table=$table&amp;id=$id&amp;text=$text_encoded&amp;backlink=$backlink_encoded" .'" data-role="button" data-ajax="false">Ja</a>';  
    $content .= '<a href="' .$backlink. '" data-role="button" data-ajax="false">Nej</a>';  
    template($header, $content);
  }

  // delete and redirect
  else{
    $table = $get['table'];
    //print_r($get);
  
    $bean = R::load($table, $get['id']);
    // don't allow deleting self
    if ($bean && $bean->removed == '0' && $bean->owner == $owner && !($table == $users_table && $bean->id == $owner)) $edit_access = true;
    else $edit_access = false;
  
    if ($edit_access){
      $bean->removed = 1;
      R::store($bean);
    }
    // backlink is two pages back
    $redirect = $get['backlink'];
    header("location: $redirect?deleted");
  }
}

else {
  header( 'Location: ./' ) ;
}
?>
