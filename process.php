<?php
require_once 'Crypt/HMAC.php';    // grab this with "pear install Crypt_HMAC"
require_once 'HTTP/Request.php';  // grab this with "pear install --onlyreqdeps HTTP_Request"
require_once('class.s3.php');
require_once('simpledb.class.php');
require_once('sqs.client.php');

define('AWS_ACCESS_KEY_ID',		'11G3J3ZASNAZ98NMB702');
define('AWS_SECRET_ACCESS_KEY',	'16theROJPj81WWCvBaQ4VhxE+BABFz94PiLkYSDH');
define('DOMAIN','photo_jobs'); //sdb
define('SQS_Q',	'photo_q'); //sqs
define('SQS_ENDPOINT',	'http://queue.amazonaws.com');

//1. Fire up SQS and get next message off queue
$q = new SQSClient(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, SQS_ENDPOINT, SQS_Q);

$nextMessage = $q->ReceiveMessage(1);

//print_r($nextMessage);

if (count($nextMessage)){

foreach($nextMessage as $message){
	//grab the message body for use in lookup on SimpleDB
	$BUCKET = urldecode($message->Body);
	$handle = $message->ReceiptHandle;
}

//2. login to simpledb
$sd = new SimpleDb(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

//echo "<pre>";
//echo $BUCKET . "<br/>";

//3. Pull out attributes we need!
$domains = $sd->listDomains();
foreach ($domains->ListDomainsResult as $domains){
	foreach ($domains as $id => $d_name){
		if ($d_name == DOMAIN){
			$mydomain = $sd->query($d_name);
			//print_r($mydomain);
			foreach ($mydomain->QueryResult as $items){
				foreach ($items as $itemid => $i_name){
					if ($i_name == $BUCKET){
						$attr = $sd->getAttributes($d_name,$i_name);
					}				
				}
			}
		}
	
	}

}

//4. Process attribute data
foreach ($attr->GetAttributesResult as $attribute){
	foreach ($attribute as $array){
		//echo $array->Name . ":". $array->Value ."<br/>";
		if ($array->Name == "email"){
			$EMAIL = $array->Value;
		}
		if ($array->Name == "path"){
			$OBJECT = $array->Value;
		}		

		if ($array->Name == "task"){
			$TASK = $array->Value;
		}
	}
}


//5. Get Object/File from S3
$s3 = new S3();
$s3->downloadObject($BUCKET,$OBJECT,"/tmp/".$OBJECT);
//echo "download*";

//6. Update Status in SimpleDB
$data["status"]		=	array('working');
$sd->putAttributes(DOMAIN,$BUCKET,$data);


//7. Process file 
createthumb('/tmp/'.$OBJECT, '/tmp/thumb_'.$OBJECT, 100,100);
//echo "thumb*";


//8. Finalize status & upload thumbnail
$data["status"]		=	array('done');
$sd->putAttributes(DOMAIN,$BUCKET,$data);

$upload = $s3->putObject( $BUCKET, $OBJECT, '/tmp/thumb_'.$OBJECT, true);
$URL = "http://ec2-67-202-47-250.compute-1.amazonaws.com/retrieve.php?b=".$BUCKET."&o=".urlencode($OBJECT);

//9. Send email
$to = $EMAIL;
$from = 'test@example.com';
$subj = "Image thumbnail is ready!";
$msg = "Your file ($OBJECT) is ready. Please go to:\r\n ". $URL. "\r\n to retrieve the image.\r\n";
mail($to, $subj,$msg, "From:$from\r\n");



//10. Delete message from queue
//$q->DeleteMessage($handle);


}//end if count($nextMessage)




function createthumb($name,$filename,$new_w,$new_h){
	$system=explode('.',$name);
	if (preg_match('/jpg|jpeg/',$system[1])){
		$src_img=imagecreatefromjpeg($name);
	}
	if (preg_match('/png/',$system[1])){
		$src_img=imagecreatefrompng($name);
	}
	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);
	if ($old_x > $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$old_y*($new_h/$old_x);
	}
	if ($old_x < $old_y) {
		$thumb_w=$old_x*($new_w/$old_y);
		$thumb_h=$new_h;
	}
	if ($old_x == $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$new_h;
	}
	$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	if (preg_match("/png/",$system[1])){
		imagepng($dst_img,$filename); 
	} else {
		imagejpeg($dst_img,$filename); 
	}
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
}

?>
