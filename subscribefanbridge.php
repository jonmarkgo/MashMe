<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>


  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  </head>
  <body>
  <div id="container1" style="text-align: center;">
  <h1><span>MashMe</span></h1>

<p>Let us mash you up.</p>
<center>
<?php
$fanbridge_key = '996a2782940634512ec7b95b19319ee7';
$fanbridge_secret = '9f6c746cf5d2dbac7aa25aaa957f9089';
$fanbridge_token = '8afb587dcab0a4682302be7f507f5ad1';
// Required files 
require "fanbridge_api.php"; 
 
// Initialization 
$fanbridge_api = new Fanbridge_Api(array( 
    'secret' => $fanbridge_secret, 
    'token' => $fanbridge_token)); 

  $res =  json_decode($fanbridge_api->call( 
        'subscriber/add', 
        array( 
            'email' => $_POST["email"], 
            'groups' => array($_GET["id"])))); 
if ($res->id) {
	echo "Successfully subscribed!";
}
else echo "Error with subscribing to FanBridge list.";
?>
</center></body></html>