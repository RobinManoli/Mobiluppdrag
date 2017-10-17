<?php
function template($title, $content, $options=null){
global $auth;
?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title>Mobiluppdrag - <?php echo $title;?>- www.mobiluppdrag.se</title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">

	<link rel="stylesheet"  href="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.css" />
	<link rel="stylesheet"  href="css/custom.css" />
<!--	<link rel="stylesheet" href="css/jquery.ui.datepicker.mobile.css" /> -->
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
<!--	<script src="js/jQuery.ui.datepicker.js"></script>
	<script>
	  //reset type=date inputs to text
	  $( document ).bind( "mobileinit", function(){
	    $.mobile.page.prototype.options.degradeInputs.date = true;
	  });
	</script>
	<script src="js/jquery.ui.datepicker.mobile.js"></script>-->
	<script>
$(document).bind("mobileinit", function(){
  $.mobile.defaultPageTransition = 'none';
  $.mobile.defaultDialogTransition = 'none';
});
  </script>
	<script src="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.js"></script>
        <script>
	$( document ).bind( "pageinit", function( event, data ){
    $('#content').css("min-height", document.height-150 + 'px'); //css("min-height", "100% !important");

    if ($('#sms_charcounter').length){
      var sms_length = 160;
      // update charcounter on pageinit
      $('#sms_charcounter').text( $('#sms_textarea').val().length + ' tecken (' + Math.ceil($('#sms_textarea').val().length/sms_length) + ' SMS)' );
      // and on change (when changing to other form element)
      $('#sms_textarea').change(function() {
      $('#sms_charcounter').text( $('#sms_textarea').val().length + ' tecken (' + Math.ceil($('#sms_textarea').val().length/sms_length) + ' SMS)' );
      });
      // and on keyup
      $('#sms_textarea').keyup(function() {
      $('#sms_charcounter').text( $('#sms_textarea').val().length + ' tecken (' + Math.ceil($('#sms_textarea').val().length/sms_length) + ' SMS)' );
      });
    }
  });
        </script>
   <script>
	</script>

</head> 

<body> 
<div data-role="page" id="page">
	<div data-role="header" data-theme="e">
		<img src="img/mobil-uppdrag.png" alt="mobiluppdrag.se" />
	</div><!-- /header -->
	<div data-role="content" data-theme="e" id="content">
            <?php if ($auth > 0) echo get_menu(); ?>
	    <?php if ($options != null && isset($options['submenu'])): ?>
		<div id="submenu" data-role="controlgroup" data-type="horizontal">
		<?php foreach ($options['submenu'] as $link=>$text): ?>
		    <a href="<?php echo $link; ?>" data-role="button" data-theme="b"><?php echo $text; ?></a>
		<?php endforeach; ?>
		</div><!-- / submenu-->
            <?php endif; ?>
		<?php echo $content;?>
	</div><!-- /content -->
	<div data-role="footer" data-theme="a">
		<h4>&copy; 2011, www.mobiluppdrag.se</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>
<?php
}



function printable_template($title, $content, $options=array()){
global $get;
?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title>Mobiluppdrag - <?php echo $title;?>- mobiluppdrag.se</title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta charset="utf-8">

	<link rel="stylesheet"  href="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.css" />
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc1/jquery.mobile-1.0rc1.min.js"></script>
        <script>
	$( document ).bind( "pageinit", function( event, data ){
		$('#content').css("min-height", document.height-150 + 'px'); //css("min-height", "100% !important");
	});
        </script>
 
</head> 

<body> 
<div data-role="page" id="page">
	<div data-role="header" data-theme="c">
		<h4>www.mobiluppdrag.se</h4>
	</div><!-- /header -->
	<div data-role="content" data-theme="c" id="content">
		<a href="<?php if (isset($get['backlink'])) echo $get['backlink']; ?>" data-role="button">Tillbaka</a>
		<?php echo $content;?>
	</div><!-- /content -->
	<div data-role="footer" data-theme="c">
		<h4>www.mobiluppdrag.se</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>
<?php
}
?>
