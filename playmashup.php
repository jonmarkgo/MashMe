<?php 
 
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
	<Say>Prepare to be mashed, yo!</Say>
    <Play>http://jonathangottfried.name/mashups/<?php echo $_REQUEST['id']; ?>.mp3</Play>
</Response>