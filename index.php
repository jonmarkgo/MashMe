<?php
print'<pre>';
error_reporting(E_ALL);
  $app_id = '3144871';
  $app_secret = 'da595545c095b5013243eaaa1b6fe4ee6e994556';
  print'<a href="http://hunch.com/authorize/v1/?app_id='.$app_id.'">Login</a>';
  if (!isset($_GET["auth_token_key"])) {
  	print'<br>not logged in';
  }
  else {
  	print'<br>logged in';
  	  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$url_keys = array('app_id'=>$app_id,'auth_token_key'=>$_GET["auth_token_key"]);
foreach ($url_keys as $key=>$val) {
	$key = utf8_encode($key);
	$val = utf8_encode($val);
}
ksort($url_keys);
$queries = array();
foreach($url_keys as $key=>$val) {
	$queries[] = urlencode($key).'='.urlencode($val);
}
$data = implode('&',$queries) . $app_secret;
$data = sha1($data);
  curl_setopt($ch, CURLOPT_URL, 'http://api.hunch.com/api/v1/get-auth-token/?auth_token_key='.$_GET["auth_token_key"].'&app_id='.$app_id.'&auth_sig='.$data);
  $result = curl_exec($ch);
$result = json_decode($result);
  curl_setopt($ch, CURLOPT_URL, 'http://api.hunch.com/api/v1/get-recommendations/?auth_token='.$result->auth_token.'&limit=5&topic_ids=list_song');
   $result2 = curl_exec($ch);
$result2 = json_decode($result2);
  foreach($result2->recommendations as $rec) {
    print'<br>'.$rec["name"];
  }
  //print_r($result2);
  }
  
print'</pre>';
  
?>