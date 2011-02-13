<?php
error_reporting(E_ALL);

$app_id = '3144871';
$app_secret = 'da595545c095b5013243eaaa1b6fe4ee6e994556';
$lastfm_key = '8b44ce767ea0461b27699b5208be6403';
$lastfm_secret = '5b7590eb924642a8e0dc7d501a864e5d';
$e_apikey = 'VZ6AQRO0XGTPJNMAF';
$fanbridge_key = '996a2782940634512ec7b95b19319ee7';
$fanbridge_secret = '9f6c746cf5d2dbac7aa25aaa957f9089';
$fanbridge_token = '8afb587dcab0a4682302be7f507f5ad1';
// Required files 
require "fanbridge_api.php"; 
 
// Initialization 
$fanbridge_api = new Fanbridge_Api(array( 
    'secret' => $fanbridge_secret, 
    'token' => $fanbridge_token)); 
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
    if ($x > 2) break;
    if (isset($rec->artist) && !array_key_exists($rec->artist,$albums) && rand(1,5) == 1) {
      $albums[$rec->artist] = $rec;
      $x++;
    }
  }
 $e_groups = json_decode($fanbridge_api->call( 
        'email_group/fetch_all'));
 $groups_create = array();
 $group_ids = array();
 foreach ($albums as $artist=>$track) {
  $no_group = TRUE;
   foreach($e_groups as $group) {
     if ($group->name == $artist) {
       $no_group = FALSE;
$group_ids[] = array('name' => $group->name, 'id' => $group->id);
     }

   }
        if ($no_group) {
       $groups_create[] = $artist;
     }
 }
foreach ($groups_create as $group) {
   $group_ids[] = array('name'=>$group,'id'=>$fanbridge_api->call( 
        'email_group/create', 
        array( 
            'name' => $group))); 
}
//print_r($group_ids);
//die();
   //     print_r($groups_create);
   //     die(); 
  $songs = array();
  foreach($albums as $artist=>$track) {
    $e_url1 = 'http://developer.echonest.com/api/v4/artist/audio?api_key='.$e_apikey.'&format=json&name='.urlencode($track->artist).'&results=1&start=0';
    curl_setopt($ch, CURLOPT_URL, $e_url1);
    $result3 = curl_exec($ch);
    $result3 = json_decode($result3);
    $songs[] = $result3->response->audio[0];
    $e_url = '';
  }
  //print_r($songs);
  $song1 = $songs[0]->url;
  $song2 = $songs[1]->url;
  $time = time();
  $song1tmp = '/tmp/song1.'.$time.'.mp3';
  $song2tmp = '/tmp/song2.'.$time.'.mp3';
  copy($song1,$song1tmp);
  copy($song2,$song2tmp);
  $mashuptmp = 'mashups/'.$time.'.mp3';
  //echo $mashuptmp;
  $output = array();
  $com = 'python afromb.py '.$song1tmp.' '.$song2tmp.' '.$mashuptmp.' .75 env';
  //echo $com;
  //echo 
  system($com);
  curl_setopt($ch, CURLOPT_URL, 'http://api.bit.ly/v3/shorten?login=jonmarkgo&apiKey=R_d46cb69c34a01e05200545a472a42312&format=txt&longUrl='.urlencode('http://127.0.0.1/'.$mashuptmp));
    $result3 = curl_exec($ch);

    echo '<h1>YOU\'VE BEEN MASHED</h1><br><b>Mashed up <i>'.$songs[0]->title.'</i> by '.$songs[0]->artist.' and <i>'.$songs[1]->title.'</i> by '.$songs[1]->artist.' for you at:<br><a href="'.$result3.'">'.$result3.'</a></b><br><br><a href="http://twitter.com/share" class="twitter-share-button" data-url="'.$result3.'" data-text="Check out my personalized MashUp!" data-count="none" data-via="jonmarkgo">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script><iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode('http://127.0.0.1/'.$mashuptmp).'&amp;layout=button_count&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe><form action="sendtophone.php?id='.$time.'" method="POST" style="display:inline;float:right;"><input type="text" name="phonenum"><input type="submit" value="Send to Phone"></form><br><br>';
    foreach($group_ids as $gid) {
     print'<form action="subscribefanbridge.php?id='.$gid["id"].'" method="POST"><input type="text" name="email" value="Your E-Mail"><input type="submit" value="Join '.$gid["name"].'\'s Mailing List"></form>';
    }
 

?>