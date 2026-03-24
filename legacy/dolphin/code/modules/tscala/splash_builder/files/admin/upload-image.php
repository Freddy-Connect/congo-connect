<?php
header('Content-type: application/json');

if($_FILES["image"]["type"] == "image/jpg" || $_FILES["image"]["type"] == "image/jpeg") {
	$image_type = 'jpg';
}
else if($_FILES["image"]["type"] == "image/x-png" || $_FILES["image"]["type"] == "image/png") {
	$image_type = 'png';
}

$id = time();
chdir('../');
move_uploaded_file($_FILES["image"]["tmp_name"], "img/" . $_POST['type'] . "/" . $_POST['type'] . "-" . $id . "." . $image_type);

echo json_encode(array('error' => 0, 'src' => $_POST['type'] . "-" . $id . "." . $image_type));

?>