<html> 
<meta charset="utf-8"> 
<lang="ja"> 

<head>
	<title>ミミミブログ</title>
	<meta http-equiv="content-type" charset="utf-8">
</head>

<body> 

　<!-タイトルをつける->
　<h3>ミミミブログ</h3></br>

<?php

header('Content-Type: text/html; charset=UTF-8');
$dataFile="content_2-2.txt";

//---------------------------------------変数の群

$name=$_GET['nickname'];
$comment=$_GET['comment'];
$pass0=$_GET['pass0'];
$ehidden2=$_GET['ehidden2'];

$delete=$_POST['delete'];
$dhidden=$_POST['dhidden'];
$dpass=$_POST['dpass'];
$edit=$_POST['edit'];
$epass=$_POST['epass'];
$ehidden1=$_POST['ehidden1'];

//------------------------------------結果の表示をわかりやすくする
$left="＊＊＊＊【";
$right="】＊＊＊＊";
$enterpass="<font color='blue'>$left-パスワードを入力してください-$right</font>";
$wrongpass="<font color='red'>$left-パスワードが間違っています-$right</font>";

//------------------------------------------------------------------------------全フォームが空白の場合

if(empty($name) && empty($comment) && empty($delete) && empty($edit) && empty($dhidden) && empty($ehidden1) && empty($ehidden2))
{ 
	//歓迎語
	echo "$left-ミミミブログへようこそ！-$right";

//------------------------------------------------------------------------------名前とコメントのどちらかが空白の場合

}elseif((empty($name) && !empty($comment)) || (!empty($name) && empty($comment)))
{
	echo "<font color='blue'>$left-お名前とコメントを同時に入力してください-$right</font>";

//------------------------------------------------------------------------------以下は機能コード
}else
{
//-----------------------------------------------------------------1、削除機能

if(!empty($dhidden) && !empty($dpass))
	{
	$posts=file($dataFile,FILE_IGNORE_NEW_LINES);
	
	//ループによって対象行を見つける
	for($n=0;$n<count($posts);$n++) 
		{
		$items=explode('<>',$posts[$n]);
		if($items[0]==$dhidden && $items[4]==$dpass)
			{
			//パスワードがあったら、「削除された」に書き換え
			$posts[$n]="＊＊$items[0]番は削除されました＊＊";
			echo "$left-$items[0]番を削除しました-$right";
			
			//対象行のパスが間違えてる
			}elseif($items[0]==$dhidden)
					{
					echo $wrongpass; //パス間違いの警告
					$delete=$dhidden;//パス間違っても番号が入力済みに維持できる
					}
		}
	//全ファイルを上書き
	$newData=$posts;
	$newData=implode("\n",$newData).PHP_EOL;
	$fp=fopen($dataFile,"w");
	fwrite($fp,$newData);
	fclose($fp);
	}
	
//パスが空白の場合
elseif(!empty($dhidden) && empty($dpass))
	{
	echo $enterpass; //パス入力の警告
	$delete=$dhidden;//パス間違っても番号が入力済みに維持できる
	}
	
//番号を入力されてからパスを要求する
elseif(!empty($delete) && empty($dpass))
	{
	echo $enterpass; 
	}

//---------------------------------------------------------------2、編集モードに入る

if(!empty($ehidden1) && !empty($epass))
	{
	$e_posts=file($dataFile,FILE_IGNORE_NEW_LINES);
	
	//ループによって対象行を見つける
	for($n=0;$n<count($e_posts);$n++) 
		{
		$items=explode('<>',$e_posts[$n]);
		
		//パスがあってる場合
		if($items[0]==$ehidden1 && $items[4]==$epass)
			{
			//編集対象の名前とコメントを取得して、新しい変数に代入して、下のHTMLで表示させる
			$enam=$items[1];
			$ecom=$items[2];
			echo "$left-編集モードです-$right";
			}
			
		//パスが間違ってる場合
		elseif($items[0]==$ehidden1)
			{
			echo $wrongpass;//間違い警告
			$edit=$ehidden1; //パス間違っても番号が入力済み状態に維持できる
			}
		 }
	}
	
//パスが空白の場合
elseif(!empty($ehidden1) && empty($epass))
	{
	echo $enterpass;
	$edit=$ehidden1;//番号入力済みで表示させる
	}
	
//番号を入力されてパスを要求する
elseif(!empty($edit) && empty($dpass))
	{echo $enterpass;}

//---------------------------------------------------------------3、編集の書き換え機能

if(!empty($name) && !empty($comment) && !empty($pass0) && empty($edit) && empty($ehidden1) && !empty($ehidden2) && empty($delete) && empty($dhidden))
	{
	$edifile=file($dataFile,FILE_IGNORE_NEW_LINES);

	//ループによって対象行を見つける
	for($e=0;$e<count($edifile);$e++) 
		{
		$edidata=explode('<>',$edifile[$e]);
		
		//対象番号が当たる行だけを書き換え
		if($edidata[0]==$ehidden2)
			{
			//新しい内容に書き換える
			$edifile[$e]=$ehidden2."<>".$name."<>".$comment.'<>'.date("Y/m/d H:i:s")."<>".$pass0."<>";
			echo "$left-$edidata[0]番の編集を完成しました-$right";
			}
		}

	//書き換えたものを含める配列を作る
	$neweData=$edifile;
	$neweData=implode("\n",$neweData).PHP_EOL;

	//その配列をファイルに上書き
	$fp=fopen($dataFile,"w");
	fwrite($fp,$neweData);
	fclose($fp);
	}

//編集モードで、パスが空白で送信されたの場合
elseif(!empty($name) && !empty($comment) && empty($pass0) && empty($ehidden1) && !empty($ehidden2))
	{
	//パス入力の警告
	echo $enterpass;

	//パスが空白でも名前とコメントを入力済みに維持する（2、のコードと重複したので、他のより良い方法があるはず）
	$ehidden1=$ehidden2;
	$e_posts=file($dataFile,FILE_IGNORE_NEW_LINES);
	for($n=0;$n<count($e_posts);$n++) 
		{
		$items=explode('<>',$e_posts[$n]);
		if($items[0]==$ehidden1)
			{
			$enam=$items[1];
			$ecom=$items[2];
			}
		}
	}

//-----------------------------------------------------------------4、コメントを書き込む機能

if(!empty($name) && !empty($comment) && !empty($pass0) && empty($delete) && empty($dhidden) && empty($ehidden1) && empty($ehidden2) && empty($edit))
	{
	$fp=fopen($dataFile,"a");
	$file=file($dataFile);
	
	$con=count($file);
	$i=$con+1;
	$newData=$i."<>".$name."<>".$comment."<>".date("Y/m/d H:i:s")."<>".$pass0."<>";
	
	fwrite($fp,$newData.PHP_EOL);
	fclose($fp);
	echo "$left-コメントを書き込みました-$right";
	}
	
//パスが空白の場合
elseif(!empty($name) && !empty($comment) && empty($delete) && empty($dhidden) && empty($ehidden1) && empty($ehidden2) && empty($edit))
{echo $enterpass;}

 }
//--------------------------------------------------------------------------------------以下はhtml部分
?>

<!-名前フォーム->
<form action="" method="get">
	</br></br>1. ニックネーム：<input type="text" name="nickname" value = "<?php echo $enam;?>"/></br>
	</br>2. コメント：<textarea name="comment" cols ="36" rows = "5" ><?php echo $ecom;?></textarea></br>
	</br>3. パスワード：<input type="text" name="pass0" /></br></br>
	
	<!-if文によって編集モードかどうかを判断する。編集モードだったらhidden2によって対象番号を転送->
	<?php if (!empty($ehidden1)) : ?>　
	<?php echo "<input type='hidden' name='ehidden2' value='$ehidden1'/>"; ?>
	<?php endif; ?>
	
	<input class="button" type="reset" value="書き直す" />
	<input class="button" type="submit" value="発信" /></br>
</form>

<!-削除フォーム->
<form action="" onsubmit="return confirm('本当に削除しますか？')" method="post">
	<p>-----------------------------------------------------------------------------</p>
	<!-if文によってパスのフォームを隠す->
	<?php if (empty($delete)) : ?>
	<?php echo "削除対象番号：<input type='number' name='delete' />"; ?>
	
	<?php else: ?>
	<?php echo "削除対象番号：".$delete;  ?>
	</br>パスワード：<input type="text" name="dpass" size="20" />
	<?php echo "<input type='hidden' name='dhidden' value='$delete' />"; ?>
	<?php endif; ?>
	
	<input class="button" type="submit" value="削除" /></br>
</form>

<!-編集フォーム->
<form action="" method="post">
	<p>-----------------------------------------------------------------------------</p>
	<!-if文によってパスのフォームを隠す->
	<?php if (empty($edit)) : ?>
	<?php echo "編集対象番号：<input type='number' name='edit' />"; ?>
	
	<?php else: ?>
	<?php echo "編集対象番号：".$edit; ?>
	<?php echo "</br>パスワード：<input type='text' name='epass' />"; ?>
	<!-2回目で編集ボタンを押す時に、対象番号をhidden1によって転送->
	<?php echo "<input type='hidden' name='ehidden1' value='$edit'/>"; ?>
	<?php endif; ?>
	
	<input class="button" type="submit" value="編集" /></br></br>
	<hr>
　</form>

<?php
//--------------------------------------------------------------------------------------以下は表示機能

$lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) 
	{
	list($_num,$_nam,$_com,$_dt,,)=explode('<>',$line);
	echo "#<b>{$_num}</b> : 　　＜".$_nam."＞さん　　　投稿時間：".$_dt."<br />";
	echo "<br />$_com<br /><br />";
	echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br />";
	}

?>

</body> 
</html>