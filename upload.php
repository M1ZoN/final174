<?php
function ft_error()
{
	echo "<div id='error'>Sorry :(<br /><img src ='https://www.elegantthemes.com/blog/wp-content/uploads/2016/03/500-internal-server-error-featured-image-1.png'></div>";
}
function tokenize($var)
{
	$salt1 = "qm&h*";
	$salt2 = "pg!@";
	return (hash('ripemd128', "$salt1$var$salt2"));
}
function sanitizeString($var)
{
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}
function sanitizeMySQL($connection, $var){
    $var = $connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}
function add_user($connection, $un, $pw, $em)
{
    $token = tokenize($pw);
    $query = "INSERT INTO users VALUES('$un', '$token', '$em')";
    $result = $connection->query($query);
    if (!$result)
        ft_error();
}
function createToken($fileLoc)
{
    $myfile = fopen($fileLoc, "r") or ft_error();
    $virus = "";
    while (!feof($myfile))
    {
        $oneByte = fgetc($myfile);
   	$token = tokenize($oneByte);
        $virus .= $token;
    }
    return $virus;
}
function addVirus($conn, $fileLoc, $filename)
{
    $virus = createToken($fileLoc);
    $query = "INSERT INTO viruses VALUES('$filename', '$virus')";
    $result = $conn->query($query);
    if (!$result)
	ft_error();
}