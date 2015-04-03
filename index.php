<html>
<body>
<h1>Thumbnailer</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<p><label for="myfile">Choose image to upload</label>
<input name="userfile" id="myfile" type="file" /></p>

<p><label for="name">Your Name</label>
<input type='text' name='fullname' id='name'/>
</p>

<p><label for="email">Your Email</label>
<input type='text' name='emailaddress' id='email'/>
</p>

<p><label for='crop'><input type='checkbox' name='task' id='thumbnail' value='thumbnail' checked='checked'/>Create Thumbnail</label></p>

<p><input type="submit" value="Upload File" /></p>

</form>
</body>
</html>