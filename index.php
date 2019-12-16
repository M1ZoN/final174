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
            <form action='index.php' method='post'> 
                <input type='submit' value='Log in or Sign Up' name='loginOrSignup' class='btn btn-primary'>
            </form><br>
            <form action='index.php' method='post' enctype='multipart/form-data'> 
                Input File To Scan:<input type='file' name='virusToScan'><input type='submit' value='Scan File' name='scan' class='btn btn-danger'>
            </form>
_END;
function auth($conn)
{
    if (isset($_POST['login']))
    {
            $un_temp = sanitizeMySQL($conn, $_POST['username']);
            $pw = sanitizeMySQL($conn, $_POST['password']);
            $query = "SELECT * FROM users WHERE username='$un_temp'";
            $result = $conn->query($query);
            if (!$result)
                ft_error();
            elseif ($result->num_rows)
            {
                $row = $result->fetch_assoc();
                $token = tokenize($pw);
                if ($token == $row[password] && $un_temp == 'root')
                {
                    echo "<br>Hi, Admin!<br>";
                    echo "<form action='admin.php'><input type='submit' value='Admin Page' name='admin' class='btn btn-warning'></form>";
                }
                elseif ($token == $row[password])
                {
                    echo "<br>Hi, contributor $row[username]<br>";
                    echo "<form action='addSearch.php'><input type='submit' value='Profile' name='userProfile' class='btn btn-warning'></form>";
                }
                else die("<br>Invalid username/password combination");
            }
            else die("<br>Invalid username/password combination");
    }
    else 
    {
        setheader('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        die ("Please enter your username and password");
    }
}
function loginOrSignup()
{
    echo<<<_LOGIN
	<br>    
	<form action='index.php' method='post'>
                Username: <input type='text' name='username' required><br>
                Email: <input type='email' name='email'><br>
                Password: <input type='password' name='password' required><br>
                <input type='submit' value='Log in' name='login' class='btn btn-success'>
                <input type='submit' value='Sign Up' name='signup' class='btn btn-success'>
            </form><br>
_LOGIN;
}
if (isset($_POST['loginOrSignup']))
    loginOrSignup();
elseif (isset($_POST['scan']))
{
	if (file_exists($_FILES['virusToScan']['tmp_name']))
	{
	    $virus = createToken($_FILES['virusToScan']['tmp_name']);
	    $search = "SELECT * FROM viruses WHERE content='$virus'";
	    $res = $conn->query($search);
	    if ($res->num_rows > 0)
		echo "<div id='warning'><br><br> WARNING IT IS A VIRUS!<br><br></div>";
	    else
		ft_error();
	}
}
elseif (isset($_POST['login']))
    auth($conn);
elseif (isset($_POST['signup']))
{
    $un = sanitizeMySQL($conn, $_POST['username']);
    $pw = sanitizeMySQL($conn, $_POST['password']);
    $em = sanitizeMySQL($conn, $_POST['email']);
    add_user($conn, $un, $pw, $em);
    auth($conn);
}
$conn->close();
echo "</body></html>";