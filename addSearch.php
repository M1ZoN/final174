<?php
require_once 'login.php';
require_once 'upload.php';
	echo<<<_END
	<html>
        <head>
            <link href="https://fonts.googleapis.com/css?family=Yeon+Sung&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <title>Form Upload</title>
            <style>
                table, th, td {
                    border: 1px solid black;
                }
                body {
                    font-size: 130%;
                    font-family: 'Yeon Sung', cursive;
                    text-align: center;
                }
                form {
                    display: inline-block;
                }
		#warning {
		    font-size: 200%;
		}
            </style>
        </head>
        <body>
            <form action='addSearch.php' enctype='multipart/form-data' method='post'>
                Upload your file: <input type='file' name='fileUpload'><input type='submit' value='Upload New Virus' name='uploadVir' class='btn btn-warning'><br>
                Check for Viruses: <input type='file' name='virusCheck'><input type='submit' value='Check!' name='checkVir' class='btn btn-danger'><br>
            </form><br>
_END;
if (isset($_POST['uploadVir']))
{
    $filename = sanitizeMySQL($conn, $_FILES['fileUpload']['name']);
    $fileLoc = $_FILES['fileUpload']['tmp_name'];
    if(file_exists($_FILES['fileUpload']['tmp_name']))
        addVirus($conn, $fileLoc, $filename);
    else
        ft_error();
}
elseif (isset($_POST['checkVir']))
{
	if (file_exists($_FILES['virusCheck']['tmp_name']))
	{
	    $virus = createToken($_FILES['virusCheck']['tmp_name']);
	    $search = "SELECT * FROM viruses WHERE content='$virus'";
	    $res = $conn->query($search);
	    if ($res->num_rows > 0)
		echo "<div id='warning'><br><br> WARNING IT IS A VIRUS!<br><br></div>";
	    else
		ft_error();
	}
}
$conn->close();
echo "</body></html>";