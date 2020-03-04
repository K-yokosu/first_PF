<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　貯金額入力ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');


//POST送信があった場合
if(!empty($_POST)){
    debug(' POST送信があります。');
    
    $seireki = $_POST['seireki'];
    $savings = $_POST['savings'];
    
    //バリデーションチェック
     //半角数字チェック
    validHalf($savings,'savings');
    //未入力チェック
    validRequired($savings,'savings');
   
    
    if(empty($err_msg)){
        debug('バリデーションOKです。');
        
        //DB接続
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO savings (year,price,user_id,create_date) VALUES(:year,:price,:user_id,:create_date)';
            $data = array(':year' => $seireki,':price' => $savings, ':user_id' => $_SESSION['user_id'] ,':create_date' => date('Y-m-d H:i:s'));
            
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            
            //クエリ成功の場合
            if($stmt){
                debug('クエリは成功しました。');
                debug('マイページへ遷移します。');
                header("Location:data-savings.php");
            }else{
                debug('クエリに失敗しました。');
                $err_msg['common'] = MSG07;
            }
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG10;
        }
    }else{
        debug('バリデーション失敗しました。');
    }
   
}else{
    debug('POST送信に失敗しました。');
}
?>

<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/data-savings.css">
   </head>
   <body>
       <header>
           <h1><a href="index.php">D.vidulum</a></h1>
           
          <!-- ナビゲーション -->
           <nav>
             <ul>
               <li><a href="index.php">ホーム</a></li>
               <li><a href="data_add.php">データ追加</a></li>
               <li><a href="logout.php">ログアウト</a></li>
             </ul>
           </nav>
       </header>
       
       <main class="main">
          <h2>貯蓄目標の項目を入力してください。</h2>
          <form action="" method="post" class="form">
　　　　　　   <p class="area-msg"><?php if(!empty($err_msg['common'])) echo '※'.$err_msg['common']; ?></p>
             <select name="seireki" class="form-content">
　　　　        　　<option value="2018年">2018年</option>
                  <option value="2019年">2019年</option>
                  <option value="2020年" selected>2020年</option>
             </select><br>
              <lavel>1年の目標貯蓄金額を入力
              <input type="text" name="savings"></lavel>
　　　　　　    <p class="area-msg"><?php if(!empty($err_msg['savings'])) echo '※'.$err_msg['savings']; ?></p>
              <input type="submit" value="登録">
          </form>
         
       </main>
       
       <section class="backpage">
           <p><a href="mypage.php">&gt;&gt;&gt;マイページへ戻る</a></p>
       </section>
       
    <!-- footer -->
       <footer id="footer">
           Copyright <a href="index.php">D.vidulum ホームページ</a>.All Rights Reserved.
       </footer>

      <script src="js/vendor/jquery-2.2.2.min.js"></script>
      <script>
         $(function(){
          var $ftr = $('#footer');
          if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
            $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
          }
        });
      </script>       
   </body>
</html>