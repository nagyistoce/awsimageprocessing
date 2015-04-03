<?php
require_once 'Crypt/HMAC.php';    // grab this with "pear install Crypt_HMAC"
require_once 'HTTP/Request.php';  // grab this with "pear install --onlyreqdeps HTTP_Request"
require_once('s3.class.php');
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


if (count($nextMessage)){

foreach($nextMessage as $message){
	//grab the message body for use in lookup on SimpleDB
	$BUCKET = urldecode($message->Body);
	$handle = $message->ReceiptHandle;
}

//2. login to simpledb
$sd = new SimpleDb(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

echo "<pre>";
echo $BUCKET . "<br/>";

//3. Pull out attributes we need!
$domains = $sd->listDomains();
foreach ($domains->ListDomainsResult as $domains){
	foreach ($domains as $id => $domain_name){
		if ($domain_name == DOMAIN){
			$mydomain = $sd->query($domain_name);
			//print_r($mydomain);
			foreach ($mydomain->QueryResult as $items){
				foreach ($items as $itemid => $item_name){
					if ($item_name == $BUCKET){
						$attr = $sd->getAttributes($domain_name,$item_name);
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
echo "HERE *JF*";
//$fh = fopen("/tmp/".$OBJECT,"x");
//fwrite($fh,$s3->getObject($OBJECT,$BUCKET));
$data = $s3->getObject($OBJECT,$BUCKET);
var_dump($data);
echo "CT=".$s3->getResponseContentType();
echo "ACL=".$s3->getAcl();
//fclose($fh);
echo "DONE *JF*";
echo $s3->getObjectInfo($OBJECT,$BUCKET);
echo "<img src='http://s3.amazonaws.com/".$BUCKET."/".$OBJECT."'/>";


//6. Update Status in SimpleDB
$data["status"]		=	array('working');
$sd->putAttributes(DOMAIN,$BUCKET,$data);





//8. Finalize status and send email
$data["status"]		=	array('done');
$sd->putAttributes(DOMAIN,$BUCKET,$data);

//9. Delete message from queue
$q->DeleteMessage($handle);


}//end if count($nextMessage)
?>
