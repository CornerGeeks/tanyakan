<?php
require_once("header.php");

if(isset($_POST["action"])){
	if($_POST["action"]=="register"&&isset($_POST["username"],$_POST["password"])){
		$existing=R::findOne("user","name = ?",array($_POST["username"]));
		if(!$existing->id){
			$user=R::dispense("user");
			$user->name=$_POST["username"];
			$user->password=hasher($_POST["password"]);
			$_SESSION["user_id"]=R::store($user);
			header("Location: admin.php");
		}
	}
	if($_POST["action"]=="login"&&isset($_POST["username"],$_POST["password"])){
		$user=R::findOne("user","name = ?",array($_POST["username"]));
		if($user->id && check_hash($_POST["password"],$user->password)){
			$_SESSION["user_id"]=$user->id;
			header("Location: admin.php");
		}
	}
	if($_POST["action"]=="logout"){
		unset($_SESSION);
		session_destroy();
	}
	
	
}

?><!doctype html>
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-responsive.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.js"></script>
</head>
<body>
<div class="container">
<?php if(@$_SESSION["user_id"]){?>
<form method="post">
<button class="btn btn-block" type="submit" name="action" value="logout">Logout</button>
</form>
<?php } else { ?>
<h2>Login</h2>
<form method="post">
<label>Username</label>
<input class="input-block-level" type="text" name="username"/>
<label>Password</label>
<input class="input-block-level" type="password" name="password"/><br/>
<button class="btn btn-block" type="submit" name="action" value="login">login</button>
</form>

<h2>Register</h2>
<form method="post">
<label>Username</label>
<input class="input-block-level" type="text" name="username"/>
<label>Password</label>
<input class="input-block-level" type="password" name="password"/>
<label>Repeat Password</label>
<input class="input-block-level" type="password" name="password2"/><br/>
<button class="btn btn-block" type="submit" name="action" value="register">Register</button>
</form>
<?php } ?>

</div>
</body>
</html>