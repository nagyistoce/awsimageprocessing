# awsimageprocessing
Automatically exported from code.google.com/p/awsimageprocessing

Processing images can be a tedious, error-prone, and repetitive task. 
It may involve many moving parts, and bandwidth or processor time that you don’t have (or can’t easily afford). 
Setting up a system that allows you to process images provided by web users could bog you down if you don’t 
have enough disk space or enough CPU to handle the demand. We decided to create a straightforward solution that 
would allow users to upload an image and then process that image with Amazon Web Services (AWS). For simplicity’s sake, 
the only processing step taken during the procedure is to thumbnail an image; however, for all practical purposes, 
we could have set up any number of processing steps. The solution we created involves these pieces:

1. An Amazon Elastic Cloud Computing (EC2) instance running Apache and PHP (including PEAR and GD to support our processing needs)
2.	An Amazon Simple Storage Service (S3) account to hold uploaded images 
3.	An Amazon SimpleDB account, to hold metadata about those images 
4.	An Amazon Simple Query Service (SQS) account, to send and receive messages that involve those images
