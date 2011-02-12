<?php
print'<pre>';
error_reporting(E_ALL);
$app_id = '3144871';
$app_secret = 'da595545c095b5013243eaaa1b6fe4ee6e994556';
$lastfm_key = '8b44ce767ea0461b27699b5208be6403';
$lastfm_secret = '5b7590eb924642a8e0dc7d501a864e5d';
$e_apikey = 'VZ6AQRO0XGTPJNMAF';
print'<a href="http://hunch.com/authorize/v1/?app_id='.$app_id.'">Login</a>';
if (!isset($_GET["auth_token_key"])) {
  print'<br>not logged in';
} else {
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
  curl_setopt($ch, CURLOPT_URL, 'http://api.hunch.com/api/v1/get-recommendations/?auth_token='.$result->auth_token.'&limit=100&topic_ids=list_album');
  $result2 = curl_exec($ch);
  $result2 = json_decode($result2);
  $x = 1;
  $albums = array();
  foreach($result2->recommendations as $rec) {
    if ($x > 5) break;
    if (isset($rec->artist) && !array_key_exists($rec->artist,$albums)) {
      $albums[$rec->artist] = $rec;
      $x++;
    }
  }
  $songs = array();
  foreach($albums as $artist=>$track) {
    $e_url1 = 'http://developer.echonest.com/api/v4/artist/audio?api_key='.$e_apikey.'&format=json&name='.urlencode($track->artist).'&results=1&start=0';
    curl_setopt($ch, CURLOPT_URL, $e_url1);
    $result3 = curl_exec($ch);
    $result3 = json_decode($result3);
    $songs[] = $result3->response->audio[0];
    $e_url = '';
  }
  print_r($songs);
}
print'</pre>';
?>