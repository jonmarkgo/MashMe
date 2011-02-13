<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>


  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
  </head>
  <body style="background: url(mashme.jpg);">
<?php

$app_id = '3144871';
$app_secret = 'da595545c095b5013243eaaa1b6fe4ee6e994556';


if (!isset($_GET["auth_token_key"])) {
print'<a class="button" href="http://hunch.com/authorize/v1/?app_id='.$app_id.'">Mash Me</a>';
?>
  <script>
  $(function() {
    $( ".button" ).button();
  });
  </script>
  <?php
} else {
  print'
<div id="loading"><h1 id="masher">MASHING</h1><img src="ajax-loader.gif"></div>';
?>
<script>
$(function() {
  jQuery.ajax({
  url: "process.php",
  data: {
    "auth_token_key": "<?php echo $_GET['auth_token_key']; ?>",
    "user_id": "<?php echo $_GET['user_id']; ?>"
  },
  dataType: "html",
  success: function(msg){
        $('#loading').after(msg);
    $('#loading').hide();

  }
});
});
</script>
<?php } ?>
</body>
</html>