<?php
require_once("header.php");

function make_pin(){
	$temp=rand(100,999); //error if there are 999 active rooms? worry about scaling?
	//oh. count active rooms if we do scale
	$exist = R::find("room","pin = ?",array($temp));
	return @$exist->id? make_pin() : $temp; //not tested
}

	

if(@$_SESSION["user_id"]){

	if(@$_POST["action"]=="deactivate"){
		$room=R::load("room",$_SESSION["room_id"]);
		$room->active=false;
		R::store($room);
		unset($_SESSION["room_id"]);
		unset($_SESSION["room_pin"]);
	}


	$user=R::load("user",$_SESSION["user_id"]);
	$room=$user->withCondition('active = true')->ownRoom;
	foreach($room as $r){
		$_SESSION["room_id"]=$r->id;
		$_SESSION["room_pin"]=$r->pin;
	}
	if(isset($_POST["new_room"])){
		if($user->id){
			$room=R::dispense("room");
			$room->time= R::isoDateTime();
			$room->active=true;
			$room->name=$_POST["new_room"];
			$pin=make_pin();
			$room->pin=$pin;
			$id=R::store($room);
			$user->ownRoom[]=$room;
			R::store($user);
			$_SESSION["room_id"]=$id;
			$_SESSION["room_pin"]=$pin;
		}
	}
	
} else {
	header("Location: login.php");
}

?><!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<style>
body {background:#FEFEFE;padding-top:40px;}
.centre {text-align:center}
canvas {margin-top:40px}
</style>
</head>
<body>

<div class="container-fluid">
<?php if(@$_SESSION["user_id"]){ ?>
<?php if(@$_SESSION["room_id"]){ ?>
<h2>Room Pin: <span class="text-success"><?php echo $_SESSION["room_pin"]; ?></span> <small>Join me</small></h2>

<ul>
<li><a href="new_question.php" class="btn btn-primary btn-large btn-block">New Question <i class="icon-white icon-plus"></i></a></li>
<li><a href="show_result.php" class="btn btn-success btn-large btn-block">Show Result <i class="icon-white icon-align-left"></i></a></li>
<li><form action="login.php" method="post"><button name="action" value="logout" class="btn btn-warning btn-large btn-block">Logout<i class="icon-white icon-remove-sign"></i></button></form></li>
<li><form method="post"><button name="action" value="deactivate" class="btn btn-danger btn-large btn-block">Exit Class<i class="icon-white icon-align-left"></i></button></form></li>
</ul>
<?php } else { ?>

<h1>Seems like you don't have a room. <small>Let's make one</small></h1>
<form id="makeRoom" method="post">
<input type="text" class="input-block-level" name="new_room" placeholder="Room Name"/>
<button class="btn btn-success btn-large btn-block">Make Room <i class="icon-white icon-plus"></i></button>
</form>
<form action="login.php" method="post"><button name="action" value="logout" class="btn btn-warning btn-large btn-block">Logout <i class="icon-white icon-remove-sign"></i></button></form>

<?php } ?>
<?php } else { ?>


<?php } ?>
</div>
</body>
</html>