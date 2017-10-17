<?php
function get_menu(){
  global $basename;
  $content = '';
  $content .= '<div data-role="controlgroup" data-type="horizontal">';
  if ( $basename == 'sms_list.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="sms_list.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">SMS</a>';
  if ( $basename == 'assignments.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="assignments.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">Uppdrag</a>';
  if ( $basename == 'locations.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="locations.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">Platser</a>';
  if ( $basename == 'tasks.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="tasks.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">Uppgifter</a>';
  if ( $basename == 'users.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="users.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">Uppdragstagare</a>';
  if ( $basename == 'profile.php') $theme = 'b'; else $theme = 'e';
  $content .= '<a href="profile.php" data-role="button" data-theme="' .$theme. '" data-ajax="false">Inställningar</a>';
  $content .= '<a href="./?logout " data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right" data-theme="a">Logga ut</a>';
  $content .= '</div>';
  return $content;
}

// gets all user's locations, if $removed == 1, it gets the deleted ones
function get_all_user_locations($removed = 0){
  global $locations_table, $owner;
  return R::find($locations_table, "removed=$removed AND owner=$owner ORDER BY location");
}
// gets all user's tasks, if $removed == 1, it gets the deleted ones
function get_all_user_tasks($removed = 0){
  global $tasks_table, $owner;
  return R::find($tasks_table, "removed=$removed AND owner=$owner ORDER BY task");
}
// gets all user's users, if $removed == 1, it gets the deleted ones
function get_all_user_users($removed = 0){
  global $users_table, $owner;
  return R::find($users_table, "removed=$removed AND owner=$owner ORDER BY name");
}

function get_user_assignments($query_type='initial', $query_data=''){
  global $assignments_table, $locations_table, $owner;
  if (!get_magic_quotes_gpc()) $query_data = addslashes($query_data);

  //$rows = R::getAll("SELECT $assignments_table.*, $locations_table.location, $locations_table.other_info AS location_info FROM $assignments_table JOIN $locations_table ON " .$locations_table."_id = $locations_table.id WHERE $assignments_table.removed=0 AND $assignments_table.owner=$owner AND location LIKE '$query_data%'");
  $rows = R::getAll("SELECT $assignments_table.* FROM $assignments_table JOIN $locations_table ON " .$locations_table."_id = $locations_table.id WHERE $assignments_table.removed=0 AND $assignments_table.owner=$owner AND location LIKE '$query_data%'");
  return R::convertToBeans($assignments_table, $rows);
}

// gets all user's smses, if $removed == 1, it gets the deleted ones
function get_user_sms($removed = 0){
  global $sent_sms_table, $received_sms_table, $owner;
  //return R::find($sent_sms_table, "removed=$removed AND owner=$owner ORDER BY sent_time DESC");
  $s = $sent_sms_table;
  $r = $received_sms_table;
//  $u_id = $users_table . '_id';
//  $query = "SELECT 'sent' as direction, id, $u_id, text, sent_time as time FROM $s UNION select 'received' as direction, id, $u_id, text, received_time as time FROM $r ORDER BY time";
  $query =  "SELECT 'sent' as type, id, sent_time as time FROM $s UNION select 'prefix' as type, id, received_time as time FROM $r WHERE assignment_start!=0 ORDER BY time DESC";
  //echo $query;
  return R::getAll($query);
}

function get_editable_bean_div($table, $bean, $theme, $display_items, $editable_items, $collapsed=true, $options=array()){
  global $users_table, $userbean, $basename, $get;
  $content = '';
  $content .= '<div data-role="collapsible" data-collapsed="' .$collapsed. '" data-theme="'.$theme.'" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3>';
  // header for collapsibles
  $header = '';
  $i = 0;
  foreach($display_items as $item)
  {
    // show saved entry value
    if ($bean){
      if ($i > 0 && trim($bean->$item) != "") $header .= ', ';
      $header .= $bean->$item;
    }
    // show new item text
    else $header .= $item;
    $i++;
  }
  if ($bean && isset($get['saved']) && isset($get['id']) && $get['id'] == $bean->id ) $header.=' &nbsp; [sparad]';
  $content .= $header;
  // add icon (if there is one) for contained data, such as email icon, phone number icon, etc
  if ($bean) foreach($bean as $item=>$value) if ($bean && $bean->$item != '' && file_exists("img/$item.png")) $content .= " <img src=\"img/$item.png\" style=\"float:right;\" /> ";
  $content .= '</h3>';

  // collapsible content
  $content .= '<p>';
  $content .= '<form action="save_bean.php" method="post" data-ajax="false">';
  $content .= '<input type="hidden" name="table" value="' .$table. '"  />';
  if ($bean) $content .= '<input type="hidden" name="id" value="'.$bean->id.'"  />';
  foreach($editable_items as $item)
  {
    if ($item == 'id') continue;
    else if ($item == 'password') {
      $content .= 'För att ändra lösenord: skriv in ditt nuvarande lösenord under "Gammalt lösenord" nedan, och skriv sedan in det nya lösenordet i de två följande fälten.';
      $content .= '<div data-role="fieldcontain">';
      $content .= '<label for="old_password">Gammalt lösenord </label>';
      $content .= '<input type="password" name="old_password" value=""  />';
      $content .= '</div>';
      $content .= '<div data-role="fieldcontain">';
      $content .= '<label for="new_password">Nytt lösenord </label>';
      $content .= '<input type="password" name="new_password" value=""  />';
      $content .= '</div>';
      $content .= '<div data-role="fieldcontain">';
      $content .= '<label for="repeat_password">Upprepa nytt lösenord </label>';
      $content .= '<input type="password" name="repeat_password" value="" />';
      $content .= '</div>';
    }
    else {
      if ($bean) $value = $bean->$item;
      else $value = "";
      if ($item == 'email') $type = 'email';
      else $type = 'text';
      $content .= '<div data-role="fieldcontain">';
      $content .= '<label for="' .$item. '">' .$item. ' </label>';
      $content .= '<input type="' .$type. '" name="' .$item. '" value="'.$value.'"  />';
      $content .= '</div>';
    }
  }
  $content .= '<button type="submit" data-theme="e" data-inline="true" name="submit" value="" class="ui-btn-hidden" aria-disabled="false">Spara</button>';
  // create remove button, but don't allow removing of self
  if ($bean && !($table == $users_table && $bean->id == $userbean->id)) $content .= '<a href="delete_bean_dlg.php?table=' .$table. '&amp;id=' .$bean->id. '&amp;text=' .urlencode(trim($header)). '" data-theme="a" data-role="button" data-inline="true" data-icon="delete">Ta bort</a>';
  if (isset($options['user_buttons'])) foreach($options['user_buttons'] as $link=>$text){
    $link = str_replace('$id', $bean->id, $link);
    $text = str_replace('$name', $bean->name, $text);
    $data = '&amp;backlink=' . urlencode($basename.'?id='.$bean->id);
    $content .= '<a href="' .$link.$data. '" data-role="button" data-inline="true" data-theme="b">' .$text. '</a>';
  }
  $content .= '</form>';
  $content .= '</p>';
  $content .= '</div><!-- /collapsible -->';
  return $content;
}

// returns a list of editable entries of table $table, the list consisting of $beans,
// the header consisting of $display_items, the $editable_items being editable when uncollapsing the collapsible
function get_editable_bean_list($table, $beans, $display_items='', $editable_items='', $options=array()){
  global $get;
  $content = '';
  $i = 0;
  if ($display_items == '') $display_items = array();
  if ($editable_items == '') $editable_items = $display_items;
  if (!isset($get['removed']) || $get['removed'] == '0') $content .= get_editable_bean_div($table, null, 'd', array('Skapa ny'), $editable_items);

  if (isset($get['id'])) $id = $get['id'];
  // iterate $beans
  foreach($beans as $bean)
  {
    if ($i%2 == 0) $theme = 'e';
    else $theme = 'd';
    if ($id == $bean->id) $collapse = 'false';
    else $collapse = 'true';
    $content .= get_editable_bean_div($table, $bean, $theme, $display_items, $editable_items, $collapse, $options);
    $i++;
  }
  return $content;
 }

function get_assignments_list(){
  global $get, $owner, $assignments_table, $locations_table, $tasks_table, $users_table;
  $content = '';
  if (isset($get['initial']) && $get['initial'] != '') $beans = get_user_assignments('initial', $get['initial']);
//  $i = 0;
  $display_items = array('id');
  $content .= '<a href="assignment_dlg.php" data-role="button" data-theme="d" data-inline="false">Skapa nytt</a>';
  $az = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'å', 'ä', 'ö');
  foreach($az as $c){
    if (strtolower($get['initial']) == $c) $theme = 'b';
    else $theme = 'e';
    $content.= '<a href="?initial=' .$c. '" data-role="button" data-inline="true" data-theme="' .$theme. '">' .$c. '</a> &nbsp; ';
  }
  // iterate $beans
//  print_r($beans);
  foreach($beans as $bean)
  {
    if ($bean->id == $get['id']) $collapsed = "false";
    else $collapsed = "true";
    $content .= '<div data-role="collapsible" data-collapsed="' .$collapsed. '" data-theme="e" data-content-theme="d">';
    $content .= '<h3>';
    $content .= '#' .$bean->id. ' ';
    $content .= $bean->$locations_table->location;
//    $header .= $bean['location'];
    $tasks = '';
    $users = '';
    foreach(R::related($bean, $tasks_table) as $task) $tasks .= $task->task . '; ';
    foreach(R::related($bean, $users_table) as $user) $users .= $user->name . ', ';
// Plats (uppgifter)';
    // remove last two chars: '; ';
    $tasks = substr($tasks, 0, -2);
    $users = substr($users, 0, -2);
    if ($tasks != '') $content .= " ($tasks)";
    if ($users != '') $content .= " ($users)";
//    $content .= '</a>';
    $content .= '</h3>';
    $content .= '<p><b>SMS ID</b>: ' .$bean->id. '</p>';
    $content .= '<p><b>Plats</b>: ' .$bean->$locations_table->location;
    if ($bean->$locations_table->other_info) $content .= ' (' .$bean->$locations_table->other_info. ')';
    $content .= '</p>';
    if ($tasks != '') $content .= "<p><b>Uppgift(er)</b>: $tasks</p>";
    if ($users != '') $content .= "<p><b>Uppdragstagare</b>: $users</p>";
    $content .= '<a href="assignment_dlg.php?id=' .$bean->id. '" data-role="button" data-theme="d" data-inline="true">Ändra</a>';
    $content .= '<a href="sms_send_dlg.php?assignment_id=' .$bean->id. '" data-role="button" data-theme="d" data-inline="true">Skicka som SMS</a>';
//    $content .= '<a href="" data-role="button" data-theme="d" data-inline="true">Kopiera</a>';
    $content .= '</div>'; 
    $i++;
  }
  return $content;
 }

// printable list for assignment takers
function get_user_assignments_list($id){
  if (!is_numeric($id)) return;
  global $users_table, $Assignments_table, $locations_table, $user_shortnumber, $user_prefix, $Tasks_table;
  $content = '';
  $bean = R::load($users_table, $id);

/*  $content .= '<p>';
  $content .= "För att starta ett uppdrag gör man så här:<br />";
  $content .= "1. När du börjar uppdraget, skriv ett SMS med ordet $start_prefix och sedan ID-nummer (se exempel nedan).<br />";
  $content .= "2. Skicka SMS:et till nummer: $shortnumber";
  $content .= '</p>';
  $content .= '<p>';
  $content .= "För att avsluta ett uppdrag gör man så här:<br />";
  $content .= "1. När uppdraget är klart, skriv ett nytt SMS med ordet $stop_prefix och sedan ID-nummer.<br />";
  $content .= "2. Skicka SMS:et till nummer: $shortnumber";
  $content .= '</p>';*/

  $content .= '<p><b>';
  $content .= $bean->name . '<br />';
  $content .= $bean->mobile_number . '<br />';
  $content .= '</b></p>';

  // assignments list
  $sharedAssignments = 'shared'.$Assignments_table;
  $sharedTasks = 'shared'.$Tasks_table;
  foreach($bean->$sharedAssignments as $assignment){
    $content .= '<p>';
    $content .= 'Uppdrag: ';
    $content .= '<b>' .$assignment->id. '</b>';
    $content .= '<br />';
    $content .= 'Plats: ';
    $content .= '<b>' .$assignment->$locations_table->location. '</b>';
    $content .= '<br />';
    $content .= 'Uppgifter: ';
    $tasks = '';
    foreach($assignment->$sharedTasks as $task) $tasks .= $task->task . '; ';
    $tasks = substr($tasks, 0, -2);
    $content .= "<b>$tasks</b>";
    $content .= '<br />';
    $content .= 'SMS skickas till: ';
    $content .= "<b>$user_shortnumber</b>";
    $content .= '<br />';
    $content .= 'SMS-text för detta uppdrag: ';
    $content .= "<b>$user_prefix " .$assignment->id. "</b>";
    $content .= '</p>';
  }
  return $content;
}

// sms colors:
// blue = new -> you should read this
// black = failed
// white = pending; no errors but incomplete
// orange = complete
function get_sms_list($removed=0){
  global $get, $owner, $sent_sms_table, $received_sms_table, $assignments_table, $locations_table, $tasks_table, $Tasks_table, $users_table;
  $content = '';
  $smses = get_user_sms();
  //  $i = 0;
  $display_items = array('id');
  $content .= '<a href="sms_send_dlg.php" data-role="button" data-theme="d" data-inline="false">Skicka SMS</a>';
  // for displaying dates in swedish
  setlocale(LC_ALL, 'sv_SE.utf8');

  //print_r($beans);
  foreach($smses as $sms)
  {
      // clear variables, not to keep wrong value upon looping
      $location = '';
      $tasks = '';
      $unix_time = '';
      if ($sms['type'] == 'prefix'){
        $bean = R::load($received_sms_table, $sms['id']);
        $header = '';
        $header .= 'Från: ';
        $header .= $bean->$users_table->name;
        $unix_time = $bean->received_time;
        // assignment sms
        if ($bean->assignment_id != null) {
          $assignment = R::load($assignments_table, $bean->assignment_id);
          $assignment_id = $assignment->id;
          $stop_sms = R::findOne($received_sms_table, "owner='$owner' AND assignment_id=$assignment_id AND assignment_start=0 AND received_time>$unix_time ORDER BY received_time ASC");
          if ($stop_sms) {
            $theme = 'e';
            $unix_time_stop = $stop_sms->received_time;
            $assignment_time = $unix_time_stop-$unix_time;
            $assignment_hours = floor($assignment_time/3600);
            $assignment_minutes = ceil(($assignment_time-3600*$assignment_hours)/60);
            $header = $assignment_hours.'h'.$assignment_minutes."m $header";
          }
          // no stop sms received
          else {
            $theme = 'c';
          }
          $location = $assignment->$locations_table->location;
          $header .= " ($location)";
          $tasks = '';
          $sharedTasks = 'shared'.$Tasks_table;
          foreach($assignment->$sharedTasks as $task) $tasks .= $task->task . '; ';
          if ($tasks != ''){
            $tasks = substr($tasks, 0, -2);
            $header .= " ($tasks)";
          }
        }
        // non assignment sms, can be used as normal message
        else {
          $theme = 'a';
          $header .= ' ('. $bean->text .')';
        }
      }
      else if ($sms['type'] == 'sent'){
        $bean = R::load($sent_sms_table, $sms['id']);
        $header = '';
        $header .= 'till:';
        $header .= $bean->$users_table->name;
        $header .= ' ('. $bean->text .')';
        $unix_time = $bean->sent_time;
        if ($bean->delivery_status == 'DELIVRD') {
          $header = "Levererat $header";
          $theme = 'e';
        }
        else if ($bean->delivery_status == 'NOT SENT') {
          $header = "Ej skickat $header";
          $theme = 'a';
        }
        else if ($bean->delivery_status == 'UNDELIV') {
          $header = "Ej levererat $header";
          $theme = 'a';
        }
        else {
          $header = "Skickat $header";
          $theme = 'c';
        }
      }
//    if ($bean->sent_time == null) $theme = 'a';
//    else $theme = 'd';
    $content .= '<div data-role="collapsible" data-collapsed="true" data-theme="' .$theme. '" data-content-theme="d">';
    $header = strftime(" %e/%m", $unix_time) . " $header";
    $content .= "<h3>$header</h3>";
//    $content .= $bean->$users_table->name;
//    $header .= $bean['location'];
//    $tasks = '';
//    $users = '';
//    foreach(R::related($bean, $tasks_table) as $task) $tasks .= $task->task . '; ';
//    foreach(R::related($bean, $users_table) as $user) $users .= $user->name . ', ';
// Plats (uppgifter)';
    // remove last two chars: '; ';
//    $tasks = substr($tasks, 0, -2);
//    $users = substr($users, 0, -2);
//    if ($tasks != '') $content .= " ($tasks)";
//    if ($users != '') $content .= " ($users)";
//    $content .= '</a>';
//    if ($bean->sent_time == null) $sent_time = 'ej skickat';
//    else $sent_time = date("Y-m-d H:i:s", $bean->sent_time);
//    $content .= '<p><b>Tid skickat</b>: ' .$sent_time. '</p>';
//    $formatted_text = str_replace(' ', '&nbsp;', $bean->text);
    //    $formatted_text = str_replace("\n", '<br />', $formatted_text);
    $display_time = strftime('%A, %e %B %Y. Kl. %H:%M', $unix_time);
    $display_time = strtoupper(substr($display_time, 0, 1)) . substr($display_time, 1);
    $content .= '</h3>';
    if ($stop_sms) {
      $content .= "<p><b>Starttid</b>: $display_time</p>";
      $stop_time = strftime('%A, %e %B %Y. Kl. %H:%M', $unix_time_stop);
      $stop_time = strtoupper(substr($stop_time, 0, 1)) . substr($stop_time, 1);
      $content .= "<p><b>Sluttid</b>: $stop_time</p>";
    }
    else $content .= "<p><b>Tid</b>: $display_time</p>";
    $text = $bean->text;
    $text = str_replace(" ", '&nbsp;', $text);
    $text = str_replace("\n", '<br />', $text);
    $content .= "<p><b>SMS Text</b>:<br />$text</p>";
    if ($location != '') $content .= "<p><b>Plats</b>: $location</p>";
    if ($tasks != '') $content .= "<p><b>Uppgifter</b>: $tasks</p>";
//    $content .= '<a href="assignment_dlg.php?id=' .$bean->id. '" data-role="button" data-theme="d" data-inline="true">Ändra</a>';
//    $content .= '<a href="sms_send_dlg.php?assignment_id=' .$bean->id. '" data-role="button" data-theme="d" data-inline="true">Skicka som SMS</a>';
//    $content .= '<a href="" data-role="button" data-theme="d" data-inline="true">Kopiera</a>';
    $content .= '</div>'; 
    $i++;
  }
  return $content;
 }
?>
