<?php
require_once 'Crypt/HMAC.php';    // grab this with "pear install Crypt_HMAC"
require_once 'HTTP/Request.php';  // grab this with "pear install --onlyreqdeps HTTP_Request"
require_once('class.s3.php');
require_once('simpledb.class.php');
require_once('sqs.client.php');

define('AWS_ACCESS_KEY_ID',		'11G3J3ZASNAZ98NMB702');
define('AWS_SECRET_ACCESS_KEY',	'16theROJPj81WWCvBaQ4VhxE+BABFz94PiLkYSDH');

//1. Create random string
// unique string used for bucket and in SDB! 
$random = md5(uniqid(rand(), true)); 


//2. Establish bucket name for S3, domain name for SimpleDB, SQS stuffs
define('BUCKET',$random);
define('DOMAIN','photo_jobs');
define('SQS_Q',	'photo_q');
define('SQS_ENDPOINT',	'http://queue.amazonaws.com');


//3. Upload file with random string as name
$s3 = new S3();
$s3->createBucket(BUCKET);


move_uploaded_file( $_FILES['userfile']['tmp_name'], "/tmp/".$_FILES['userfile']['name'] );
chmod( "/tmp/".$_FILES['userfile']['name'], 0777 );

$fh = fopen( "/tmp/".$_FILES['userfile']['name'], 'rb' );
$contents = fread( $fh, filesize( "/tmp/".$_FILES['userfile']['name'] ) );
fclose( $fh );

echo "Bucket=".BUCKET;
echo "Type=".$_FILES['userfile']['type'];

$attempt = $s3->putObject( BUCKET, $_FILES['userfile']['name'], "/tmp/".$_FILES['userfile']['name'], true);


//4. login to simpledb
$sd = new SimpleDb(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

//5. make sure that our domain is created
$sd->createDomain(DOMAIN);

//6. Put in job ticket
$data["fullname"]	=	array($_POST['fullname']);
$sd->putAttributes(DOMAIN,$random,$data);
$data["email"] 		=	array($_POST['emailaddress']);
$sd->putAttributes(DOMAIN,$random,$data);
$data["status"]		=	array('not started');
$sd->putAttributes(DOMAIN,$random,$data);
$data["path"]		=	array($_FILES['userfile']['name']);
$sd->putAttributes(DOMAIN,$random,$data);
$data["task"]		=	array($_POST['task']);
$sd->putAttributes(DOMAIN,$random,$data);


//7. set up SQS Queue and send message
$q = new SQSClient(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, SQS_ENDPOINT);

try{
	$result = $q->CreateQueue(SQS_Q);
	//echo 'Queue Created: ', $result, "\n<br />\n";
}catch(Exception $e){
	throw($e);
}


//message will consist of $random value generated topside!
$messageId = $q->SendMessage(urlencode($random));
//echo 'Message sent, message id: ', $messageId, "\n<br />\n";


//8. Send email
$to = $_POST['emailaddress'];
$from = 'test@example.com';
$subj = "Your image has been uploaded!";
$msg = "You will receive an email once the file has been processed.\r\n";
$msg .= "MessageID: ". $messageId. "\r\n";
$msg .= "Bucket: ". $random;
mail($to, $subj,$msg, "From:$from\r\n");


//9. Redirect
header('Location:thanks.php');
?>
