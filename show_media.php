<?php

//データベースの接続
$dsn = 'mysql:dbname=co_924_it_99sv_coco_com;host=localhost';
$user = 'co-924.it.99sv-coco.com';
$password = 'Jbui7hY';
$pdo = new PDO($dsn,$user,$password);
//文字化け対策
$stmt=$pdo->query('SET NAMES utf8');
header('Content-Type: text/html; charset=UTF-8');

//対象番号を取得
$i = $_GET["no"];

//対象行を抽出
$result = $pdo -> query("SELECT * FROM keijiban where comment_id=$i;") -> fetch();

//バイナリデータを転送する
header("Content-Type: ".$result["type"]);
echo ($result["binarydata"]);
	
?>