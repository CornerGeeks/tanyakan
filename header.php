<?php
require_once("rb.php");
$dbname="REPLACE_DB_NAME";
$dbuser="REPLACE_DB_USER";
$dbpassword="THIS_IS_ACTUALLY_MY_REAL_PASSWORD";
R::setup("mysql:host=localhost;dbname=$dbname",$dbuser,dbpassword);
session_start();

function strip_slashes_recursive($mixed){
	if(is_string($mixed))
    	return stripslashes($mixed);	
    if(is_array($mixed))
        foreach($mixed as $i=>$value)
            $mixed[$i]=strip_slashes_recursive($value); 
    return $mixed; 
}

function hasher($p,$h=""){
  if($h)return $h==crypt($p,substr($h,0,strpos($h,"$",20)+1));
  for($i=0;$i<16;$i++)$h.=chr(rand(64,126));
  return crypt($p,'$5$rounds=5000$'.$h.'$'); 
}
function check_hash($p,$h){
	return $h==hasher($p,$h);
}

if (get_magic_quotes_gpc()){ //!! ideally disable the magic
	$_GET=strip_slashes_recursive($_GET);
	$_POST=strip_slashes_recursive($_POST);
	$_COOKIE=strip_slashes_recursive($_COOKIE);
}
?>