<?php

// variable setting
$name = $_REQUEST['name'];
$email = $_REQUEST['Email'];
$message = $_REQUEST['Message'];
$subject = "Contact | aidan-a.me";
$to = "contact@aidan-a.me";  // change receiving email id 

// check input fields
if (empty($name) || empty($email) || empty($message)) {
    echo "<script type='text/javascript'>alert('Please fill all correct');
          window.history.go(-1);
          </script>";
} else {
    // get the uploaded file
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_ext = strtolower(end(explode('.', $file_name)));
    
    // set the allowed file extensions
    $extensions = array("pdf", "doc", "docx", "txt");
    
    if (in_array($file_ext, $extensions) === false) {
        echo "<script type='text/javascript'>alert('Extension not allowed, please choose a pdf, doc, docx or txt file.');
              window.history.go(-1);
              </script>";
    } else if ($file_size > 2097152) { // set the maximum file size (in bytes)
        echo "<script type='text/javascript'>alert('File size must be less than 2 MB.');
              window.history.go(-1);
              </script>";
    } else {
        // open the file for reading and encoding
        $file = fopen($file_tmp, "rb");
        $data = fread($file, $file_size);
        fclose($file);
        $data = chunk_split(base64_encode($data));  // encode the file data
        
        // set the email headers
        $boundary = md5(time());
        $headers = "From: " . $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";
        
        // set the email body
        $message .= "\r\n\r\n--$boundary\r\n";
        $message .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
        $message .= "$data\r\n\r\n--$boundary--\r\n";
        
        // send the email
        if (mail($to, $subject, $message, $headers)) {
            echo "<script type='text/javascript'>alert('Your message sent succesfully.');
                  window.history.go(-1);
                  </script>";
        } else {
            echo "<script type='text/javascript'>alert('Failed to send email, please try again later.');
                  window.history.go(-1);
                  </script>";
        }
    }
}

?>
