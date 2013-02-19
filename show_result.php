<?php
require_once("header.php");
unset($_SESSION["question_id"]);

?><!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<style>
body {background:#FEFEFE;padding-top:20px;}
canvas {margin-top:40px}
#room_info {position:fixed;bottom:0px;right:5px;opacity:0.6;margin-bottom:0px;-webkit-transition: opacity 0.5s;}
#room_info:hover {opacity:1;}
#room_info a:hover {text-decoration:none}
#question {text-align:center;margin-top:80px}
#legend li {margin-bottom:5px}
#myModal {width:900px;margin-left:-450px}
</style>
<script>
	
$(function(){
var canvas=document.getElementById("c")
var ctx=canvas.getContext("2d");

function make_result(r){
		for(var i=0,data=[];i<r.data.responses.length;i++){
			data.push(0);
		}
		for(var i=0,label=[],data=[];i<r.result.length;i++){
			data[Number(r.result[i].response)-1]=Number(r.result[i].num);
		}
		$("#question").text(r.data.question)
		canvas.width=600;
		canvas.height=500;


		var colors = ["rgb(73, 175, 205);", "rgb(81, 163, 81);", "rgb(248, 148, 6);", "rgb(189, 54, 47);"];
		var colorClass=["info","success","warning","danger"]
		var center = [canvas.width / 2, canvas.height / 2];
		var radius = Math.min(canvas.width, canvas.height-20) / 2;
		var lastPosition = 0, total = 0;
		
		for(var i in data) { total += data[i];}
		for (var i = 0; i < data.length; i++) {
			if(data[i]){
			ctx.fillStyle = colors[i%colors.length];
			ctx.beginPath();
			ctx.moveTo(center[0],center[1]);
			ctx.arc(center[0],center[1],radius,-Math.PI/2+lastPosition,-Math.PI/2+lastPosition+(Math.PI*2*(data[i]/total)),false);
			ctx.lineTo(center[0],center[1]);
			ctx.fill();
			lastPosition += Math.PI*2*(data[i]/total);
			}
		}
		
		$("#legend").html("")
		for (var i = 0,lastPosition=0; i < data.length; i++) {
			if(data[i]){
			ctx.fillStyle = "#FFF"; //colors[i%colors.length];
			ctx.strokeStyle="#FFF";
			ctx.lineWidth=3;
			ctx.font = " 50px 'Helvetica Neue'";
			ctx.textAlign="center";
			ctx.textBaseline = 'middle';
			
			$("#legend").append($("<li/>").append($("<button/>").text(r.data.responses[i]).attr({class:"btn btn-large btn-block btn-"+colorClass[i%colorClass.length]})))
		
			//x = cx + r * cos(a)
			//y = cy + r * sin(a)
			ctx.fillText(data[i], center[0]+radius/2*Math.cos(-Math.PI/2+lastPosition+Math.PI*2*(data[i]/total/2)),center[1]+radius/2*Math.sin(-Math.PI/2+lastPosition+Math.PI*2*(data[i]/total/2)));
			//ctx.strokeText(data[i], center[0]+radius/2*Math.cos(-Math.PI/2+lastPosition+Math.PI*2*(data[i]/total/2)),center[1]+radius/2*Math.sin(-Math.PI/2+lastPosition+Math.PI*2*(data[i]/total/2)));
		
			lastPosition += Math.PI*2*(data[i]/total);
			}
	}
}

	function load_result(){
		$.getJSON('api.php?result',function(data){
			make_result(data);
			setTimeout(load_result,2000)
		})
	}
	load_result();
	//$("#myModal").modal("show")
})
</script>
</head>
<body>

<div class="container-fluid">
<?php if(@$_SESSION["room_id"]){ ?>

 
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">x</button>
    <h3 id="myModalLabel">Instructions</h3>
  </div>
  <div class="modal-body">
  <div class="hero-unit">
 	<h1>Go to this address: <span class="text-warning"><?php echo $_SERVER['SERVER_NAME'],dirname($_SERVER['SCRIPT_NAME']); ?></span></h1>
  <h1>Enter pin :  <span class="text-success"><?php echo $_SESSION["room_pin"]; ?></span></h1>
</div>
  </div>
</div>
<h2 id="room_info"><a href="#myModal" role="button" data-toggle="modal">Address: <span class="text-warning"><?php echo $_SERVER['SERVER_NAME'],dirname($_SERVER['SCRIPT_NAME']); ?></span> Room Pin: <span class="text-success"><?php echo $_SESSION["room_pin"]; ?></span> <small>Join me</small></a></h2>
<div class="row">
<div class="span7"><canvas id="c">[No canvas support]</canvas></div>
<div class="span5">
<h2 id="question"></h2>
<ul id="legend"></ul>
</div>
</div>
<?php } else { ?>

<?php } ?>
</div>
</body>
</html>