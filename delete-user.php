<?php
require_once "include/header.php";

if(isset($_GET['uid'])){
	$userId = $_GET['uid'];
	$currentUserInfo = $user_obj->selectUserInfo($userId);
}

if(isset($_POST['confirm']) && $_POST['confirm'] === "delete"){
	echo "YAOIJASJDLKSJLKSJLKDKLWJS";
}

if(isset($_POST['confirm']) && $_POST['confirm'] === "back"){
	header("Location: edit-user.php?uid={$userId}");
}

?>

<div class="container mt-2">
    <div class="row">
		<h2>Are you sure you want to delete <?= $currentUserInfo['data']['u_name']?></h2>
		<form action="" method="post">
				<button type="submit" class="btn btn-danger" name="confirm" value="delete">Delete</button>
				<button type="submit" class="btn btn-primary" name="confirm" value="back">Back</button>
			</form>
	</div>
</div>