<?php
require_once("header.php");
if(isset($_SESSION["room_id"])){
if(isset($_GET["result"])){
	if(!isset($_SESSION["question_id"])){
		$question=R::findOne("question","room_id = ? order by time desc",array($_SESSION["room_id"]));
		$_SESSION["question_id"]=$question->id;
		$_SESSION["question_data"]=serialize(array("time"=>$question->time,"question_id"=>$question->id,"question"=>$question->question,"responses"=>unserialize($question->responses)));
	}
	$result=R::getAll("select response, count(response) num from response,(SELECT max(id) id FROM response where question_id=? group by user) m where response.id=m.id group by response",array($_SESSION["question_id"]));
	echo json_encode(array("data"=>unserialize($_SESSION["question_data"]),"result"=>$result));
} else if(isset($_POST["response"],$_POST["question_id"])){
	$question=R::load("question",$_POST["question_id"]);
	if($question->id){
	$response=R::dispense("response");
	$response->user = session_id();
	$response->room = $_SESSION["room_id"];
	$response->time =  R::isoDateTime();
	$response->response = intval($_POST["response"]);
	$id=R::store($response);
	$question->ownResponse[]=$response;
	R::store($question);
	echo json_encode(array("status"=>"success"));
	}
} else {	
	$question=R::findOne("question","room_id = ? order by time desc",array($_SESSION["room_id"]));
	if($question->id){
		echo json_encode(array("time"=>$question->time,"question_id"=>$question->id,"question"=>$question->question,"responses"=>unserialize($question->responses)));
	}
}
}
?>