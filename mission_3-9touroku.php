<html> 
<meta charset="utf-8"> 
<lang="ja"> 

<head>
	<title>ミミミブログへの登録</title>
	<meta http-equiv="content-type" charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style1.css" media="screen" />
</head>

<body> 

　<!-タイトルをつける->
　<div id="head"><h1>ミミミブログ・登録</h1></br></div>

<?php

//------------------------------------------------------データベースの接続

$dsn = 'mysql:dbname=co_924_it_99sv_coco_com;host=localhost';
$user = 'co-924.it.99sv-coco.com';
$password = 'Jbui7hY';
$pdo = new PDO($dsn,$user,$password);
//文字化け対策
$stmt=$pdo->query('SET NAMES utf8');
header('Content-Type: text/html; charset=UTF-8');

//---------------------------------------------------------変数の定義

$user_name=$_GET['user_name'];
$password=$_GET['password'];
$email=$_GET['email'];
$regflag=0;
$regtime=date("YmdHis");

$left="＊＊＊＊【";
$right="】＊＊＊＊";

//---------------------------------------------------------------歓迎語

if(empty($user_name) && empty($password) && empty($email))
	{echo "$left-ご登録ください-$right";}

//------------------------------------------------名前、パス、メールアドレスのどれかが空の場合

elseif(empty($user_name))
	{echo "<font color='blue'>$left-ユーザー名を入力してください-$right</font>";}
elseif(empty($password))
	{echo "<font color='blue'>$left-パスワードを入力してください-$right</font>";}
elseif(empty($email))
	{echo "<font color='blue'>$left-メールアドレスを入力してください-$right</font>";}

//----------------------------------------------------------------------本番の登録コード

elseif(!empty($user_name) && !empty($password) && !empty($email))
	{

	//ニックネームの唯一性
	$result = $pdo -> query("select * from user where user_name='$user_name'") -> fetch();
	if(!empty($result))
		{
		echo '<script>alert("この名前はすでに存在しているので、他のにしてください。");window.history.go(-1);</script>';
		exit;
		}

	//メールアドレスの正しさ
	$pattern="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
	if(!preg_match($pattern,$email))
		{
		echo '<script>alert("正しいメールアドレスを入力してください。");window.history.go(-1);</script>';
		exit;
		}

	//メールアドレスの唯一性
	$result = $pdo -> query("select * from user where email='$email'") -> fetch();
	if(!empty($result))
		{
		echo '<script>alert("このメールアドレスはすでに使用されているので、他のにしてください。");window.history.go(-1);</script>';
		exit;
		}
		
	//トークンを生成する
	$token=md5(uniqid());

	//--------------------------------------------------変数をテーブルに代入
	
	$sql = $pdo -> prepare("INSERT INTO user (user_name,password,email,token,regflag,regtime) VALUES(:user_name,:password,:email,:token,:regflag,:regtime);");
	$sql -> bindParam(':user_name',$user_name,PDO::PARAM_STR);
	$sql -> bindParam(':password',$password,PDO::PARAM_STR);
	$sql -> bindParam(':email',$email,PDO::PARAM_STR);
	$sql -> bindParam(':token',$token,PDO::PARAM_STR);
	$sql -> bindParam(':regflag',$regflag,PDO::PARAM_INT);
	$sql -> bindParam(':regtime',$regtime,PDO::PARAM_STR);
	$sql->execute();

	echo "$left-登録しました-$right";
	
	//---------------------------------------------------メール送信機能
	//クリックされてほしいログインのURL、それにトークンをつける
	$url="http://co-924.it.99sv-coco.com/mission_3-9login.php?token=".$token;
	
	$to=$email; //発信先
	
	$from= "From: cstc1105@icoud.com"; //発信源
	
	$title="「ミミミブログ」からの認証メール"; //メールのタイトル

	//メール内容
	$message= $user_name."さん：ご登録ありがとうございます。以下のURLをクリックして本登録を完成させましょう。".$url."。クリックしても反応がないなら、お手数ですが、ブラウザにコピペしてください。（※本登録のURLへのアクセスは、メールをお送りしてから24時間に限り有効です。）--------　ミミミブログより";
	
	//発信する
	$send=mail($to,$title,$message,$from);
	
	//メール発信成功
	if($send==true)
		{
		echo "</br>ご記入のメールアドレスに認証メールを発信しました。メールに添付されたURLをクリックして本登録を完成させましょう！</br>URLが付いてるので、「受信箱」にないなら「ゴミ箱」に入ってしまうもしれません。そちらのチェックもお願いします。</br>※本登録のURLへのアクセスは、Eメールをお送りしてから<font color='blue'>24時間</font>に限り有効です。</br></br><font color='red'>＊＊10分以上経過してもメールが届いていない場合、陳までお知らせください＊＊</font>";
		}
	
	//万が一、発信失敗だったら
	else
		{
		echo "<font color='red'>$left-システムの原因により、メール送信が失敗しました。もう一度お試しください。-$right</font>";
		}
	}

//------------------------------------------------------htmlのコード
?>


<!-登録フォーム->
<form action="" method="get">
	</br></br>1. ユーザー名：<input type="text" name="user_name" id="user_name"/></br>
	</br>2. パスワード：<input type="text" name="password" id="password"/></br>
	</br>3. メールアドレス：<input type="text" name="email" id="email"/></br></br>
	
	<a href="mission_3-9login.php">登録済みの方はログインへ</a>　　
	<input class="button" type="submit" value="登録" /></br>
<hr>
</form>
		
</body> 
</html>







