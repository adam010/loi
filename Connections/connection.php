<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connection = "localhost";
$database_connection = "dbloi1";
$username_connection = "root";
$password_connection = "";
$connection = mysql_pconnect($hostname_connection, $username_connection, $password_connection) or trigger_error(mysql_error(),E_USER_ERROR); 
/*$mail["host"] = "example.com"; 
$mail["port"] = "25"; 
$mail["auth"] = true; 
$mail["username"] = "user"; 
$mail["password"] = "password";

$headers   = array();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/pl

$headers[] = "From: Sender Name <sender@domain.com>";
$headers[] = "Bcc: JJ Chong <bcc@domain2.com>";
$headers[] = "Reply-To: Recipient Name <receiver@domain3.com>";*/
?>