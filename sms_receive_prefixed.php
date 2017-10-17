<?php
//header("Content-Type: text/html; charset=ISO-8859-9");  
require('init.php');
$content = '';
$debug = false;

// add auth here, ie authorized server address or similar
if (1){
  if (isset($post['nr'])){
      // leveransvariabler som mottags ser ut som följande (POST) [variablenman]=[variabelvärde]:
      // sms:et var till nr 71120 med text: "sfr a b c /"
      // [smsid]=[4004579][messageid]=[404B19B0FFB14D13ACDE6287C45F9E11][nr]=[46737019379][prefix]=[sfr][operator]=[Tele2][text]=[a b c /][destnr]=[71120][mtstatus]=[0][price]=[0]
      // POST data
      $smsid = $post['smsid'];
      $messageid = $post['messageid'];
      $nr = $post['nr'];
      $prefix = strtolower($post['prefix']);
      $operator = $post['operator'];
      $text = $post['text'];
      $destnr = $post['destnr'];
      $mtstatus = $post['mtstatus'];
      $price = $post['price'];

      if (!get_magic_quotes_gpc()){
        $smsid = addslashes($smsid);
        $messageid = addslashes($messageid);
        $nr = addslashes($nr);
        $prefix = addslashes($prefix);
        $operator = addslashes($operator);
        $text = addslashes($text);
        $destnr = addslashes($destnr);
        $mtstatus = addslashes($mtstatus);
        $price = addslashes($price);
      }

      // find owner of received sms
      $userdata1 = R::find($userdata_table, "datakey='prefix' AND datavalue='$prefix'");
      $userdata2 = R::find($userdata_table, "datakey='shortnumber' AND datavalue='$destnr'");
      if ($userdata1 && $userdata2 && $userdata1 == $userdata2) $owner = $userdata->$users_table->id;
      else $owner = $admin_user_id;

      // find sms sender's user bean
      // get last 9 digits of mobile number, ie 737019370
      // for either 0737019370 or 46737019370
      $mobile_nr_match = substr($nr, -9);
      $sender = R::findOne($users_table, "owner='$owner' AND mobile_number LIKE '%$mobile_nr_match'");
      if (!$sender){
          $sender_user = R::dispense($users_table);
          $sender_user->owner = $owner;
          $sender_user->removed = 0;
          $sender_user->mobile_number = $nr;
          R::store($sender_user);
      }
      else $sender_user = R::load($users_table, $sender->id);

      // find sms's assignment bean
      $trimmed_text = trim($text);
      $assignment = R::findOne($assignments_table, "owner='$owner' AND id='$trimmed_text'");
      // find last sms from same user and assignment
      if ($assignment){
        $users_table_id = $users_table . '_id';
        $sender_id = $sender_user->id;
        $assignment_id = $assignment->id;
        $last_user_assignment = R::findOne($received_sms_table, "owner='$owner' AND $users_table_id='$sender_id' AND assignment_id='$assignment_id' ORDER BY received_time DESC");
      }
      if ($last_user_assignment && $last_user_assignment->assignment_start == 1) $assignment_start = 0;
      // if no previous sms for this assignment/user, or last one was not a start one
      else $assignment_start = 1;

      // save sms in db
      $user = R::load($users_table, $owner);
      $new_sms = R::dispense($received_sms_table);
      $new_sms->owner = $owner;
      $new_sms->removed = 0;
      $new_sms->text = $text;
      $new_sms->$users_table = $sender_user;
      if ($assignment){
        $new_sms->assignment_id = $assignment->id;
        $new_sms->assignment_start = $assignment_start;
      }
      $new_sms->received_time = date("U");
      $new_sms->remote_addr = $server['REMOTE_ADDR'];
      //$new_sms->remote_host = $server['REMOTE_HOST'];
      //$new_sms->remote_port = $server['REMOTE_PORT'];
      R::store($new_sms);
      die();


  
/*
// ursprungskod, spara alla get/post-variabler som strängar
$get = '';
foreach ($_GET as $key => $value)
{
  $get.= "[$key]=>[$value]";
}

$post = '';
foreach ($_POST as $key => $value)
{
  $post.= "[$key]=>[$value]";
}
  $get = mysql_real_escape_string( $get );
  $post = mysql_real_escape_string( $post );

  $query = "INSERT INTO sms_mottagna (sms_id,text) VALUES (-1, 'get: $get --- post: $post')";
  //$query = "UPDATE sms_skickade SET text='$get <-> $post' WHERE custom_id='abcdef7'";
  $result = mysql_query($query);*/
print_r($_GET);
print_r($_POST);

  } // end isset post
}  // end "auth"

else {
  header( 'Location: ./' ) ;
}
?>
