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
  curl_setopt($ch, CURLOPT_URL, 'http://api.hunch.com/api/v1/get-recommendations/?auth_token='.$result->auth_token.'&limit=100&topic_ids=list_album');
   $result2 = curl_exec($ch);
$result2 = json_decode($result2);
//print'<br><br><b>Hunch:</b><br>';
//print_r($result2);
$x = 1;
$albums = array();
foreach($result2->recommendations as $rec) {
  if ($x > 5) break;
  if (isset($rec->artist) && !array_key_exists($rec->artist,$albums)) { $albums[$rec->artist] = $rec; $x++; }
}
//print_r($albums);
//die();
/*print'<br><br><b>Last.fm:</b><br>';
$x = 0;
require("phpbrainz/phpBrainz.class.php");
$mb = new phpBrainz();

//print_r($mb->getASINFromTrackMBID("7a408099-5c69-4f53-8050-6b15837398d1"));

//print_r($mb->getTrack());
$tracks = array();
foreach($albums as $art=>$rec) {
 // if ($x > 5) break;
//$matches = array();
//preg_match('/^http:\/\/hunch\.com\/(.+)\/.+\/.+\//',$rec->url,$matches);

//print_r($matches);
//$album_url = str_replace('-',' ',$matches[1]);
$mb_rf = new phpBrainz_TrackFilter(
    array(
     //   "title"=>$rec->name,
        "release"=>$rec->title,
        "artist"=>$rec->artist
        ));
//print_r($mb_rf);
$find = $mb->findTrack($mb_rf);
$track = array_rand($find);
$tracks[$art] = $find[$track];*/
//print_r($track);
//die();
/*$rapikey = '2qdn56a8hr3bkb5gdnk5bhkr';
$rsecret = 'RFB4mZsKfq';
$timestamp = gmdate('U'); // 1200603038  
$rsig = md5($rapikey . $rsecret . $timestamp); 
$rurl = 'http://api.rovicorp.com/data/v1/album/info?album='.$album_url.'&apikey='.$rapikey.'&sig='.$rsig;
$album_url = '';
 curl_setopt($ch, CURLOPT_URL, $rurl);
  $result3 = curl_exec($ch);
$result3 = json_decode($result3);
print_r($result3);
}*/
/*
$lurl = 'http://ws.audioscrobbler.com/2.0/?method=album.search&album='.$album_url.'&api_key='.$lastfm_key;
   curl_setopt($ch, CURLOPT_URL, $lurl);
  $result5 = curl_exec($ch);
//$result5 = json_decode($result5);
$xml = simplexml_load_string($result5);
if (!isset($xml->results->albummatches->album)) continue;
print_r($xml);
$x++;
$lurl = '';
}*/
print'<br><br><b>Echonest:</b><br>';
$songs = array();
 foreach($albums as $artist=>$track) {
  $e_url1 = 'http://developer.echonest.com/api/v4/artist/audio?api_key='.$e_apikey.'&format=json&name='.urlencode($track->artist).'&results=1&start=0';
curl_setopt($ch, CURLOPT_URL, $e_url1);
  $result3 = curl_exec($ch);
$result3 = json_decode($result3);
//print_r($result3); 
/*die();
     $e_url = 'http://developer.echonest.com/api/v4/song/search?api_key='.$e_apikey.'&title='.urlencode($track->getTitle()).'&artist='.urlencode($artist).'&bucket=id:7digital&bucket=audio_summary&bucket=tracks';
      curl_setopt($ch, CURLOPT_URL, $e_url);
  $result3 = curl_exec($ch);
$result3 = json_decode($result3);*/
$songs[] = $result3->response->audio[0];
//print_r($result3);
$e_url = '';
  }
print_r($songs);

  //print_r($result2);
  }
  
print'</pre>';
  
?>