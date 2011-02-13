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
    // Include the PHP TwilioRest library 
    require "twilio.php";
    
    // Twilio REST API version 
    $ApiVersion = "2010-04-01";
    
    // Set our AccountSid and AuthToken 
    $AccountSid = "AC75a34a5b4f997a0fac291ad12a393b41";
    $AuthToken = "dfeee30b2f0be0434df1a7314898c03f";
    
    // Outgoing Caller ID you have previously validated with Twilio 
    $CallerID = '8452088589';
    
    // Instantiate a new Twilio Rest Client 
    $client = new TwilioRestClient($AccountSid, $AuthToken);

    $data = array(
    	"From" => $CallerID, 	      // Outgoing Caller ID
    	"To" => $_POST["phonenum"],	  // The phone number you wish to dial
    	"Url" => "http://jonathangottfried.name/playmashup.php?id=".$_GET['id']
    );
    
    $response = $client->request("/$ApiVersion/Accounts/$AccountSid/Calls", 
       "POST", $data); 
    
    // check response for success or error
    if($response->IsError)
    	echo "Error starting phone call: {$response->ErrorMessage}\n";
    else
    	echo "Sent mashup to ".$_POST['phonenum']."!";
?>
</center></body></html>