<?
require('init.php');

// add auth here, ie authorized server address or similar
if (1){
  if (1){
    // leveransvariabler som mottags ser ut som följande (POST) [variablenman]=[variabelvärde]:
    // [nr]=[ 46737019379][ref]=[83564754][state]=[DELIVRD][datetime]=[2011-05-16 07:03:00][text]=[lekke][customid]=[]
    // POST data
    $sms_id = $get['sms_id'];
    $delivery_time = $post['datetime'];
    $delivery_status = $post['state'];

    if (!get_magic_quotes_gpc()){
      $sms_id = addslashes($sms_id);
      $delivery_time = addslashes($delivery_time);
      $delivery_status = addslashes($delivery_status);
    }

    $sms_bean = R::load($sent_sms_table, $sms_id);
    // if sms id not found, create new bean for debugging/dehacking
    if (!$sms_bean){
      $sms_bean = R::dispense($sent_sms_table);
    }

    $sms_bean->delivery_status = $delivery_status;
    $sms_bean->delivery_time = $delivery_time;
    $sms_bean->remote_addr = $server['REMOTE_ADDR'];
    //$sms_bean->remote_host = $server['REMOTE_HOST'];
    //$sms_bean->remote_port = $server['REMOTE_PORT'];
    R::store($sms_bean);
  } // isset post
} // auth
/*// testing code for saving all received variables
$get = '';
foreach ($_GET as $key => $value)
{
  $get.= "[$key]=[$value]";
}

$post = '';
foreach ($_POST as $key => $value)
{
  $post.= "[$key]=[$value]";
}
  $get = mysql_real_escape_string( $get );
  $post = mysql_real_escape_string( $post );

  $query = "UPDATE sms_skickade SET text='$get <-> $post' WHERE avsandare='SFR'";
  $result = mysql_query($query);
print_r($_POST);
*/

?>
