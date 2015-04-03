<?php
session_start();
require_once 'Crypt/HMAC.php';    // grab this with "pear install Crypt_HMAC"
require_once 'HTTP/Request.php';  // grab this with "pear install --onlyreqdeps HTTP_Request"
require_once('class.s3.php');
require_once('simpledb.class.php');
require_once('sqs.client.php');


define('DOMAIN','photo_jobs'); //sdb
define('AWS_ACCESS_KEY_ID',		'11G3J3ZASNAZ98NMB702');
define('AWS_SECRET_ACCESS_KEY',	'16theROJPj81WWCvBaQ4VhxE+BABFz94PiLkYSDH');


//1. Process GET vars
$BUCKET = $_GET['b'];
$OBJECT = $_GET['o'];

$_SESSION['o'] = $OBJECT;
$_SESSION['b'] = $BUCKET;

//2. Get Object/File from S3
$s3 = new S3();
$file = $s3->getObject($BUCKET,$OBJECT);
echo '<img src="http://'.$BUCKET.'.s3.amazonaws.com/'.$OBJECT.'"/>';

echo "<p>Download the image to your desktop and then 
<a href='./cleanup.php'>finalize the clean-up process</a>.";


//3. Clean up SimpleDB
$sd = new SimpleDb(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

$names = array('fullname', 'email', 'status', 'path', 'task');
$sd->deleteAttributes(DOMAIN,$BUCKET, $names);

?>