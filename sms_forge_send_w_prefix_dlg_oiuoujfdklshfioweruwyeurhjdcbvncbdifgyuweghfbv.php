<?php
require('init.php');
$content = '';

if ($auth > 0){
  // show form
  $content .= '<div data-role="collapsible" data-collapsed="false" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3>Skicka SMS</h3>';
  $content .= '<form action="sms_receive_prefixed.php" method="post" data-ajax="false">';
  // skickar data genom internet som tolkas som om ett sms med prefix mottagits
  // leveransvariabler ut som följande (POST) [variablenman]=[variabelvärde]:
  // sms:et var till nr 71120 med text: "sfr a b c /"
  // [smsid]=[4004579][messageid]=[404B19B0FFB14D13ACDE6287C45F9E11][nr]=[46737019379][prefix]=[sfr][operator]=[Tele2][text]=[a b c /][destnr]=[71120][mtstatus]=[0][price]=[0]

  $content .= 'smsid: <input name="smsid" type="text" value="debug" />';
  $content .= '<br />';
  $content .= 'messageid: <input name="messageid" type="text" value="debug" />';
  $content .= '<br />';
  $content .= 'sender number: <input name="nr" type="text" value="46737019379" />';
  $content .= '<br />';
  $content .= 'prefix: <input name="prefix" type="text" value="debug" />';
  $content .= '<br />';
  $content .= 'operator: <input name="operator" type="text" value="debug" />';
  $content .= '<br />';
  $content .= '<textarea name="text">text without prefix</textarea>';
  $content .= '<br />';
  $content .= 'destination number: <input name="destnr" type="text" value="debug" />';
  $content .= '<br />';
  $content .= '<input type="submit" />';
  $content .= '</form>';
  $content .= '</div><!-- collapsible -->';
  template('Skicka SMS', $content);
}

else {
  header( 'Location: ./' ) ;
}

?>

