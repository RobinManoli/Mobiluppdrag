<?php
require('init.php');
$content = '';

if ($auth > 0){
  // show form
  $content .= '<div data-role="collapsible" data-collapsed="false" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3>Skicka SMS</h3>';
  $content .= '<form action="sms_send.php" method="post" data-ajax="false">';

/*  // sender
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="sender" class="select">Skicka från:</label>';
  $content .= '<select name="sender" id="sender">';
  $senders = array($prefix=>$prefix, $shortnumber=>"SMS-nummer ($shortnumber)", $userbean->mobile_number=>'Mitt nummer (' .$userbean->mobile_number. ')');
  foreach($senders as $value=>$text) $content .= '<option value="' .$value. '">' .$text. '</option>';
  $content .= '</select>';
  $content .= '</div>';*/

  // receivers
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="receivers" class="select">Skicka till:</label>';
  $content .= '<select name="receivers[]" id="receivers" multiple="multiple" data-native-menu="false">';
  $assignment_bean = R::load($assignments_table, $get['assignment_id']);
  foreach(get_all_user_users() as $user){
    if (isset($assignment_bean) && R::areRelated($user, $assignment_bean)) $selected = " selected";
    else $selected = "";
    $content .= '<option value="' .$user->id. '"' .$selected. '>' .$user->name. ' (' .$user->mobile_number. ')</option>';
  }
  $content .= '</select>';
  $content .= '</div>';

  // sms textarea
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="sms_textarea">Text:</label>';
  $content .= '<textarea name="sms_textarea" id="sms_textarea" style="height:160px;">';
  $textarea = '';
  $textarea .= 'ID: ' . $assignment_bean->id;
  $textarea .= "\n\n";
  $textarea .= 'Plats: ' . $assignment_bean->$locations_table->location;
  $textarea .= "\n\n";
  $textarea .= 'Uppgifter: ';
  $tasks = '';
  foreach(R::related($assignment_bean, $tasks_table) as $task){
    $tasks .= $task->task . '; ';
  }
  //print_r($assignment_bean);
  //print_r($assignment_bean->$tasks_table);

  $tasks = substr($tasks, 0, -2); // remove last trailing '; '
  $textarea .= $tasks;
  $content .= $textarea;
  $content .= '</textarea>';
  $content .= ' &nbsp; <span id="sms_charcounter"></span>';
  $content .= '</div>';

  $content .= '<button type="submit" data-theme="e" data-inline="false" name="submit" value="send" class="ui-btn-hidden" aria-disabled="false">Skicka</button>';
  $content .= '</form>';
  $content .= '</div><!-- collapsible -->';
  template('Skicka SMS', $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
