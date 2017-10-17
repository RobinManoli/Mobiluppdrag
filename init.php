<?php
session_start();

//echo "init.php";

date_default_timezone_set('Europe/Stockholm');

$post = $_POST;
$request = $_REQUEST;
$get = $_GET;
if (isset($get['logout'])) $_SESSION = array();
$session = $_SESSION;

$server = $_SERVER;
$arr = explode('?', basename($server['REQUEST_URI']));
$basename = $arr[0]; // filename of url without get data
$referer = $server['HTTP_REFERER'];
$arr = explode('?', $referer);
$backlink = $arr[0]; // referer without get data

$users_table = 'mu_user';
$userdata_table = 'mu_userdata';
$locations_table = 'mu_location';
$tasks_table = 'mu_task';
$prefix_table = 'mu_prefix';
$sent_sms_table = 'mu_sms_sent';
$received_sms_table = 'mu_sms_received';

// get capitalized table names, ie mu_user becomes Mu_user
$Users_table = strtoupper(substr($users_table, 0, 1)) . substr($users_table, 1);
$Userdata_table = strtoupper(substr($userdata_table, 0, 1)) . substr($userdata_table, 1);
$Locations_table = strtoupper(substr($locations_table, 0, 1)) . substr($locations_table, 1);
$Tasks_table = strtoupper(substr($tasks_table, 0, 1)) . substr($tasks_table, 1);
$Prefix_table = strtoupper(substr($prefix_table, 0, 1)) . substr($prefix_table, 1);
$ownUserdata = "own$Userdata_table";

require('rb.php');
R::setup("mysql:host=mobiluppdrag.se.mysql;dbname=mobiluppdrag_se", 'mobiluppdrag_se', 'acJnN5pC');
require('functions.php');
require('template.php');
$auth = 0;
$admin_user_id = 1;
// if login form is sent
if (isset($post['email']) && isset($post['password'])){
  $email = $post['email'];
  if (!get_magic_quotes_gpc()) $email = addslashes($email);
  $password = $post['password'];
  $sha1pw = sha1($password);
}
else if (isset($session['email']) && isset($session['password'])) {
  $email = $session['email'];
  if (!get_magic_quotes_gpc()) $email = addslashes($email);
  $sha1pw = $session['password'];
}

$userbean = R::findOne($users_table, "email='$email' and password='$sha1pw'");
$owner = $userbean->id;
// if email and password are correct
if ($userbean) {
  $auth = 1;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $sha1pw;
  $session = $_SESSION;
//  $userbean = R::load($users_table, $user['id']);
//echo $userbean->id;

  $userdata = $userbean->$ownUserdata;
  // set userdata as variables such as prefix -> user_prefix = $prefix_value
  foreach($userdata as $data){
    $var = 'user_' . $data['datakey'];
    $$var = $data['datavalue'];
    //echo "$var = " .$data['datavalue'];
  }
}
// failed login attempt
else if (isset($email)){
  $auth = -1;
}

/*$u = R::load($users_table, 1);
$u->name = "Robin Manoli";
$u->mobile_number = "0737019379";
R::store($u);*/

/*$user = R::dispense($users_table);
$new_id = R::store($user);
$user->owner = $user->id;
$user->removed = 0;
$user->email = 'a@a.a';
$user->password = sha1('');
$user->name = 'Ditt namn';
// initiate number as string to keep leading zeroes
$user->mobile_number = 'Ditt mobiltelefonnummer';

$userdata1 = R::dispense($userdata_table);
$userdata2 = R::dispense($userdata_table);
$userdata3 = R::dispense($userdata_table);
// mobile gateway variables
$userdata1->datakey = 'prefix';
$userdata1->datavalue = 'test';
$userdata2->datakey = 'shortnumber';
$userdata2->datavalue = '71120';
$userdata3->datakey = 'gateway';
$userdata3->datavalue = 'mosms';

$user->$ownUserdata = array($userdata1, $userdata2, $userdata3);
R::store($user);//*/

$assignments_table = 'mu_assignments_' . $userbean->id . '_' . $user_prefix;
$Assignments_table = strtoupper(substr($assignments_table, 0, 1)) . substr($assignments_table, 1);

?>
