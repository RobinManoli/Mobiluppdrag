<?php
require('init.php');
$content = '';

//echo "index.php";
if ($auth < 1){
  // info
  $content .= '<div data-role="collapsible" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3><img src="img/info.png" alt="Information" /></h3>';
  $content .= '<p>Funktioner.</p>';
  $content .= '</div><!-- /collapsible -->';

  // register
  if (!isset($post['register']))
  {
    $register = '';
    $collapsed = "true";
  }
  else {
    $register = $post['register'];
    $collapsed = "false";
  }
  $content .= '<div data-role="collapsible" data-collapsed="' .$collapsed. '" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3><img src="img/register.png" alt="Anmäl intresse" /></h3>';
  if ($register != ''){
    mail('info@mobiluppdrag.se', 'intresseanmälan', $register);
    if (!get_magic_quotes_gpc()) $register = addslashes($register);
    // if email isn't added
      if (!R::find($users_table, "email='$register'")){
        $content .= "<p>$register är nu registrerad.</p>";
    }
    else {
      $content .= "<p>Fel: $register är redan registrerad.</p>";
    }
  }
  else {
    //$content .= '<form action="cgi-bin/FormMail.pl" method="post">';
    $content .= '<form action="" method="post">';
    $content .= '<div data-role="fieldcontain">';
    $content .= '<label for="register">E-mail</label>';
    $content .= '<input type="email" name="register" id="register" value="" />';
    $content .= '</div>';
    $content .= '<button type="submit" data-theme="c" name="submit" value="submit-value" class="ui-btn-hidden" aria-disabled="false">Anmäl intresse</button>';
    $content .= '</form>';
  }
  $content .= '</div><!-- /collapsible -->';

  // contact
  $content .= '<div data-role="collapsible" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3><img src="img/contact.png" alt="Kontakt" /></h3>';
  $content .= '<p>';
//  $content .= 'Anmäl intresse för tjänsten genom att klicka på "registrera" ovan.<br />';
  $content .= 'För frågor nås vi på e-mail: <a href="mailto:info@mobiluppdrag.se">info@mobiluppdrag.se</a>';
  $content .= '</p>';
  $content .= '</div><!-- /collapsible -->';

  // login
  if ($auth < 0)
  {
    $failed_login = "<p>Fel email eller l&ouml;senord. " .$session['password'].$post['password']. "a</p>";
    $collapsed = "false";
  }
  else {
    $failed_login = "";
    $collapsed = "true";
  }
  $content .= '<div data-role="collapsible" data-collapsed="' .$collapsed. '" data-theme="e" data-content-theme="d" class="ui-collapsible">';
  $content .= '<h3><img src="img/login.png" alt="Logga in" /></h3>';
  $content .= '<form action="" method="post">';
  if ($failed_login != "") $content .= "<p>$failed_login</p>";
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="email">E-mail</label>';
  $content .= '<input type="email" name="email" id="email" value="'.$email.'"  />';
  $content .= '</div>';
  $content .= '<div data-role="fieldcontain">';
  $content .= '<label for="password">L&ouml;senord</label>';
  $content .= '<input type="password" name="password" id="password" value=""  />';
  $content .= '</div>';
  $content .= '<button type="submit" data-theme="c" name="submit" value="submit-value" class="ui-btn-hidden" aria-disabled="false">Skicka</button>';
  $content .= '</form>';
  $content .= '</div><!-- /collapsible -->';

  template('V&auml;lkommen', $content);
 }

else {
  header( 'Location: sms_list.php' ) ;
}
?>
