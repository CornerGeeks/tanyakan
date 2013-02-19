<?php
require_once("header.php");

function make_pin(){
	$temp=rand(100,999); //error if there are 999 active rooms? worry about scaling?
	//oh. count active rooms if we do scale
	$exist = R::find("room","pin = ?",array($temp));
	return @$exist->id? make_pin() : $temp; //not tested
}

if(@$_SESSION["user_id"]){
	if(isset($_POST["new_room"])){
		$user=R::load("user",$_SESSION["user_id"]);
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

	if(isset($_POST["question"],$_POST["response"])){
		$room=R::load("room",$_SESSION["room_id"]);
		if($room->id){
			$question = R::dispense("question");
			$question->question=$_POST["question"];
			$question->responses=serialize($_POST["response"]);
			$question->time=R::isoDateTime();
			$_SESSION["question_id"]=R::store($question);
			$_SESSION["question_data"]=serialize(array("time"=>$question->time,"question_id"=>$question->id,"question"=>$question->question,"responses"=>unserialize($question->responses)));
			$room->ownQuestion[] = $question;
			R::store($room);
			header('Location: admin.php');
		}
	}
}

?><!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script>
$(function(){
	$("#addResponse").click(function(){
		var newDiv=$("<div/>").addClass("input-prepend");
		var newInput=$("<input/>").attr({
			class:"input-block-level",
			type:"text",
			name:"response[]"
		})
		var newButton=$("<span />").html("<i class='icon-minus'></i>").attr({
			class:"add-on"
		})
		newButton.click(function(){
			newDiv.remove();
		});
		newDiv.append(newButton);
		newDiv.append(newInput)
		$("#responses").append(newDiv);
		return false;
	})
})
</script>

<style>
body {background:#FEFEFE;padding-top:40px;}
.centre {text-align:center}
</style>
</head>
<script>
$(function(){
	$("#	
})
</script>
<body>

<div class="container-fluid">
<?php if(@$_SESSION["user_id"]){ ?>
<?php if(@$_SESSION["room_id"]){ ?>
<h1>Room Pin: <span class="text-success"><?php echo $_SESSION["room_pin"]; ?></span> <small>Join me</small></h1>

<h2>
<form method="post">
<textarea  name="question" class="input-block-level"></textarea>
<div id="responses"></div>
<button id="addResponse" class="btn btn-success btn-large btn-block">Add Response <i class="icon-white icon-plus"></i></button>
<button class="btn btn-primary btn-large btn-block">Save Question<i class="icon-white icon-plus"></i></button>
</form>

<?php } else { ?>

<h1>Seems like you don't have a room. <small>Let's make one</small></h1>
<form id="makeRoom" method="post">
<input type="text" class="input-block-level" name="new_room" placeholder="Room Name"/>
<button class="btn btn-success btn-large btn-block">Make Room <i class="icon-white icon-plus"></i></button>
</form>

<?php } ?>
<?php } else { ?>


<?php } ?>
</div>
</body>
</html>