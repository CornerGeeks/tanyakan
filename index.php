<?php
require_once("header.php");

if(isset($_POST["room_pin"])){
	$room=R::findOne("room","active = true and pin = ?",array($_POST["room_pin"]));
	if($room->id){
		$_SESSION["room_id"]=$room->id;
	}
}

if(isset($_POST["action"])){
	if($_POST["action"]=="leave_room"){
		unset($_SESSION["room_id"]);
	}
}

?><!DOCTYPE html>
<html>
<head>
<title>Tanyakan</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<style>
body {background:#FEFEFE;padding-top:40px;}
</style>
</head>
<body>
<div class="container-fluid">
<?php if(@$_SESSION["room_id"]){ ?>
<h1>Current Question:</h1>
<div id="question">
<p class="lead"></p>
<hr />
<form method="post"></form>
</div>
<script>

var col=["info","success","warning","danger"]
var current=0,current_question=0;
function make_button(id,response,type){
	return $("<button/>").addClass("btn-"+col[id%col.length]).addClass("btn btn-large btn-block").text(response).attr("type","submit").attr("name","response").val(1+id);
}

function create_question(q){
	if(q.time!=current){
		current=q.time;
		current_question=q.question_id;
		var obj=$("#question")
		obj.find(".lead").text(q.question)
		var objResponse = obj.find("form")
		objResponse.html("")
		objResponse.append($("<input />").attr({
			type:"hidden",
			name:"question_id",
			value:q.question_id
		}))
		$.each(q.responses,function(k,v){
			objResponse.append(make_button(k,v));
		})
		objResponse.submit(function(){
			var val = $("button[type=submit][clicked=true]").val()
			$.post('api.php',{"response":val,"question_id":current_question},function(data){})
			return false;
		})
		obj.find("button[type=submit]").click(function() {
			objResponse.find("button").removeAttr("clicked");
			$(this).attr("clicked","true");
		});
	}
}

function load_question(){
	$.getJSON('api.php',function(data){
		create_question(data)
	})
	setTimeout(load_question,10000);
}

$(function(){	
	load_question();
})
</script>
<h1>Class over?</h1>
<form method="post"><button class="btn btn-danger btn-large btn-block" name="action" value="leave_room"><i class="icon-white icon-fire"></i> Leave Room ;_;</button></form>
<?php } else { ?>

<h1>Seems like you don't have a room. <small>Let's join one</small></h1>
<form id="makeRoom" method="post">
<input type="text" class="input-block-level" name="room_pin" placeholder="Enter Room Pin Number"/>
<button class="btn btn-primary btn-large btn-block">Join Room <i class="icon-white icon-plus"></i></button>
</form>
<a href="admin.php" class="btn btn-success btn-large btn-block">Want to make a room? <i class="icon-white icon-plus"></i></a>


<?php } ?>


</div>
</body>
</html>