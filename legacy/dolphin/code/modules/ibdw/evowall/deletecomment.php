<?php
require_once('../../../inc/header.inc.php');
//comment id
$id=(int)$_POST['id'];
//action id
$id_post=(int)$_POST['idpost'];
$queryup="UPDATE bx_spy_data SET PostCommentsN=PostCommentsN-1 WHERE id=".$id_post;
$resultup = mysql_query($queryup);
$queryd1="DELETE FROM commenti_spy_data WHERE id=".$id;
$resultqueryd1=mysql_query($queryd1);
$queryd2="DELETE FROM datacommenti WHERE IDCommento=".$id;
$resultqueryd2=mysql_query($queryd2);
?>