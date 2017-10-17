<?php
require('init.php');
$content = '';

if ($auth > 0){
  // load assignment
  if (isset($request['id'])){
    $id = $request['id'];
    $bean = R::load($assignments_table, $id);
  }

  // save data 
  // print_r($post);
  if (isset($post['submit'])){
    // prepare data
    // create new assignment
    if (!isset($id)){
      $bean = R::dispense($assignments_table);
      $bean->owner = $owner;
      $bean->removed = 0;
    }
    // remove id, not trying to update it
    else unset($post['id']);
    $backlink = $post['backlink'];

    // save assignment data
    if ($bean->owner == $owner){
      $submit = $post['submit'];
      $location = $post['location'];
      unset($post['submit']);
      unset($post['backlink']);
      unset($post['location']);
  
      //print_r($bean);
      // $bean->import doesn't work with multiple-select-array
      // so, create an array that contains each multiple-select-array
      $many_to_many = array();
      foreach($post as $key=>$value){
        if (is_array($value)){
          //$bean->$key[] = $value;
          $many_to_many[$key] = $value;
          unset($post[$key]);
        }
      }
      //print_r($many_to_many);
      //print_r($post);
  
      // import post, now without arrays
      $bean->import($post);
      $bean->$locations_table = R::load($locations_table, $location);
      //R::store($bean);
  
      // associate $bean with all multiple-selected values
      foreach($many_to_many as $table_name=>$arr){
        // clear earlier associations
        R::clearRelations($bean, $table_name);
        foreach($arr as $related_bean_id){
          $related_bean = R::load($table_name, $related_bean_id);
          //print_r($);
          R::associate($bean, $related_bean);
        } // end foreach
      } // end foreach
    } // end if ($bean->owner == $owner)
    if ($submit == 'go back'){
      //if ( strpos($backlink, '?') === false ) $redirect = "$backlink";
      //else $redirect = "$backlink&id=".$bean->id; // don't use $id, as it might be new bean
      $bean_id =  $bean->id;
      $initial = strtolower(substr($bean->$locations_table->location, 0, 1));
      header("location: assignments.php?id=$bean_id&initial=$initial");
    }
    // send as sms, disabled, id must be tested, both with new assignment and edited one
/*    else if ($submit == 'send'){
      header("location: sms_send_dlg.php?assignment_id=".$bean->id);
}*/
  }
    
  // display dlg
  $content .= '<form action="assignment_dlg.php" method="post" data-ajax="false">';
  $content .= '<input type="hidden" name="backlink" value="' .$referer. '"  />';
  if (isset($id)) $content .= '<input type="hidden" name="id" value="' .$id. '"  />';
  // options menu for locations
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="location" class="select">Plats:</label>';
  $content .= '<select name="location" id="location">';
  foreach(get_all_user_locations() as $location){
    if ($bean->$locations_table->id == $location->id) $selected = " selected";
    else $selected = "";
    $content .= '<option value="' .$location->id. '"' .$selected. '>' .$location->location. '</option>';
  }
  $content .= '</select>';
  $content .= '</div>';

  // options menu for tasks
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="task" class="select">Uppgift(er) för just detta uppdrag:</label>';
  $content .= '<select name="' .$tasks_table. '[]" id="task" multiple="multiple" data-native-menu="false">';
  foreach(get_all_user_tasks() as $task){
    if (isset($bean) && R::areRelated($task, $bean)) $selected = " selected";
    else $selected = "";
    $content .= '<option value="' .$task->id. '"' .$selected. '>' .$task->task. '</option>';
  }
  $content .= '</select>';
  $content .= '</div>';

  // options menu for users 
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="user" class="select">Uppdragstagare som kan tänkas göra detta uppdrag:</label>';
  $content .= '<select name="' .$users_table. '[]" id="user" multiple="multiple" data-native-menu="false">';
  foreach(get_all_user_users() as $user){
    if (isset($bean) && R::areRelated($user, $bean)) $selected = " selected";
    else $selected = "";
    $content .= '<option value="' .$user->id. '"' .$selected. '>' .$user->name. ' (' .$user->mobile_number. ')</option>';
  }
  $content .= '</select>';
  $content .= '</div>';

// datum
/*  $content .= '<div data-role="collapsible" data-theme="e">';
  $content .= '<h3>Lägg till datum för uppdraget</h3>';
  $content .= '<label for="date">Datum:</label>';
  $content .= '<input type="true" data-type="date" data-theme="d" name="date" id="date" value="" class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-d" />';
//  $content .= '<input type="date" name="date" id="date" value="" />'; 
  $content .= '</div>';*/

  // display submit buttons
//  $content .= '<button type="submit" data-theme="e" data-inline="true" name="submit" value="send" class="ui-btn-hidden" aria-disabled="false">Spara och skicka som SMS</button>';
  $content .= '<button type="submit" data-theme="e" data-inline="true" name="submit" value="go back" class="ui-btn-hidden" aria-disabled="false">Spara</button>';
  $content .= '<a href="' .$referer. '" data-role="button" data-inline="true" data-theme="a" data-icon="delete" data-iconpos="right">Avbryt, spara inte</a>';
  $content .= '</form>';
  
  if (isset($id)) template("Uppdrag $id", $content);
  else template("Nytt uppdrag", $content);
}

else {
  header( 'Location: ./' ) ;
}
?>
