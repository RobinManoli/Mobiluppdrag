<?php
//header("Content-Type: text/html; charset=ISO-8859-9");  
require('init.php');
$content = '';
$debug = false;

if ($auth > 0){
  if (isset($post['receivers']))
  {
    //print_r($post);
  	// Include http-object and xml
	  require("sms_http_client.php");

    // POST data
    $sender = $user_shortnumber;
    $text = $post['sms_textarea'];
    if (!get_magic_quotes_gpc()) $text = addslashes($text);
    $receivers = $post['receivers'];
	
    $text = str_replace('&','+',$text); // cannot send & char thorugh sms-teknik's gateway
    $text = str_replace('<','',$text); // nor < or > chars
    $text = str_replace('>','',$text);

    foreach ($receivers as $receiver){
      $user = R::load($users_table, $receiver);
      $new_sms = R::dispense($sent_sms_table);
      $new_sms->owner = $owner;
      $new_sms->removed = 0;
      $new_sms->$users_table = $user;
      $new_sms->text = $text;
      $new_id = R::store($new_sms);

      $mobile_number = $user->mobile_number;

      // skapa xml-kod för sms-utskick
      $request = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<smsteknik>
	<id>SFR</id>
	<user>sms6#VDA2</user>
	<pass>JsCe8c</pass>
	<operationtype>0</operationtype>
	<flash>0</flash>
	<multisms>1</multisms>
	<maxmultisms>6</maxmultisms>
	<compresstext>0</compresstext>
	<text>$text</text>
	<udh></udh>
	<smssender>$sender</smssender>
	<deliverystatustype>3</deliverystatustype>
	<deliverystatusaddress>http://www.mobiluppdrag.se/sms_sent_update_delivery_status_3uer8934789uy7r7fayeuetyo23tsdhjkv.php?sms_id=$new_id</deliverystatusaddress>
  <recipients>
  <nr>$mobile_number</nr>
  </recipients>
</smsteknik>";

      // Send POST data
      $con = new HTTP_Client("www.smsteknik.se", 80);
      $con->set_path("/webservices/SendSMSws1/Httppostws1.aspx");
      if($request!=""){
        $con->post_data = $request;
        $con->send_request();
      }

      // Write the result
      $sms_result = $con->result;
      if ($debug){
        echo "sms_result: [$sms_result]";
        echo '<br />';
        echo '<br />';
        $f_request = str_replace('<', '&lt;', $request);
        $f_request = str_replace('>', '&gt;', $f_request);
        echo "Request:[<pre>$f_request</pre>]";
      }

      /*$sms_result = substr( $sms_result, 232 ); // ta bort konstant resultat-info
      // resultatet kvar är om fel, först content-length och sedan felet (eller annan info), ex: '87 0:Error, parse error. An error occurred while parsing EntityName. Line 11, position 11.'
      while ( is_numeric(substr($sms_result,0,1)) ) $sms_result = substr($sms_result,1); // ta bort content-length siffrorna
      $sms_result = trim( $sms_result );
      echo "sms-result-trimmed: [$sms_result]";
      if ( substr($sms_result,0,7) == '0:Error' ) // fel vid skickning*/

      // example response:
/*
    [0] => HTTP/1.1 200 OK
    [1] => Cache-Control: private
    [2] => Content-Type: text/html; charset=utf-8
    [3] => Server: Microsoft-IIS/7.5
    [4] => X-AspNet-Version: 2.0.50727
    [5] => X-Powered-By: ASP.NET
    [6] => Date: Thu, 20 Oct 2011 13:31:20 GMT
    [7] => Connection: close
    [8] => Content-Length: 198
    [9] => 
    [10] => <?xml version="1.0" encoding="UTF-8"?><response><datetime>2011-10-20 15:31:21</datetime><count>1</count><smsleft>4584</smsleft><sms><smsid>0:Invalid Dest Addr #+4673701937a#</smsid></sms></response>
 */
      $sms_result_arr = explode("\n", $sms_result);
      // HTTP/1.1 200 OK Cache-Control
      $response_arr = explode(" ", $sms_result_arr[0]);
      $response_type = $response_arr[1]; // 200

      // find xml response data
      foreach($sms_result_arr as $key=>$data){
        if (substr($data,0,5) == '<?xml')
        {
          $xml_key = $key;
          break;
        }
      }
      // if & char is sent in sms, response is without xml:
      // 0:Error, parse error. An error occurred while parsing EntityName. Line 13, position 21.
      /* create array of
 * (sms not sent) <?xml version="1.0" encoding="UTF-8"?><response><datetime>2011-10-20 15:31:21</datetime><count>1</count><smsleft>4584</smsleft><sms><smsid>0:Invalid Dest Addr #+4673701937a#</smsid></sms></response>
        such as $sms_data['datetime'] => 2011-10-20 15:31:21, etc
 * (sms sent) <?xml version="1.0" encoding="UTF-8"?><response><datetime>2011-10-20 15:54:38</datetime><count>1</count><smsleft>4578</smsleft><sms><smsid>105892064</smsid></sms></response>*/
      $sms_data = json_decode(json_encode((array) simplexml_load_string($sms_result_arr[$xml_key])),1);
      // now, if the receiver number is not a correct number, we get $sms_data['sms']['smsid'] => 0:Invalid Dest Addr #+4673701937a#

      // if everything is right, save sent time
      if ($response_type == '200' && isset($xml_key) && isset($sms_data['sms']['smsid']) && is_numeric($sms_data['sms']['smsid']) )
      {
        // unix time
        $new_sms->sent_time = date("U");
        $new_sms->delivery_status = 'SENT';
      }
      // otherwise, set sms with no sent time
      else{
        // echo $response_type;
        // echo isset($xml_key);
        // if (isset($sms_data['sms']['smsid'])) echo 'set';
      //else echo 'not set';
        //if (is_numeric($sms_data['sms']['smsid'])) echo 'num';
        //else echo 'not num';
        //echo $sms_data['sms']['smsid'];

        $new_sms->sent_time = date("U");
        $new_sms->delivery_status = 'NOT SENT';
        $new_sms->error_message = $sms_result;
      }
      R::store($new_sms);

    } // end foreach receivers
  } // end isset post receivers
  header("Location: sms_list.php");

}  // end auth > 0
else {
  header( 'Location: ./' ) ;
}
?>
