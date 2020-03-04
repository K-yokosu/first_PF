<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　収入額入力ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//POST送信があった場合
if(!empty($_POST)){
    debug(' POST送信があります。');
    
    $seireki = $_POST['seireki'];
    $month = $_POST['month'];
    $price = $_POST['price'];
    
    //バリデーションチェック
    //収入額_半角数字チェック
    validHalf($price,'price');
    //収入額_未入力チェック
    validRequired($price,'price');
    
    
    if(empty($err_msg)){
        debug('バリデーションOKです。');
        
        //DB接続
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO income (year,month,price,user_id,create_date) VALUES(:year,:month,:price,:user_id,:create_date)';
            $data = array(':year' => $seireki, ':month' => $month, ':price' => $price, ':user_id' => $_SESSION['user_id'] ,':create_date' => date('Y-m-d H:i:s'));
            
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            
            //クエリ成功の場合
            if($stmt){
                debug('クエリは成功しました。');
                debug('データ追加ページへ遷移します。');
                header("Location:data-income.php");
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
       <link rel="stylesheet" href="css/data-income.css">
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
          <h2>収入額の項目を入力してください。</h2>
          <form action="" method="post" class="form">
　　　　　　   <p class="area-msg"><?php if(!empty($err_msg['common'])) echo '※'.$err_msg['common']; ?></p>
             <select name="seireki" class="form-content">
　　　　        　　<option value="2018年">2018年</option>
                  <option value="2019年">2019年</option>
                  <option value="2020年" selected>2020年</option>
                 
             </select>
　　　　　　　　<select name="month" class="form-content">
　　　　　　　　　　　<option value="1月" <?php if($current_month == "1月") echo 'selected'; ?>>1月</option>
　　　　　　　　　　　<option value="2月" <?php if($current_month == "2月") echo 'selected'; ?>>2月</option>
　　　　　　　　　　　<option value="3月" <?php if($current_month == "3月") echo 'selected'; ?>>3月</option>
　　　　　　　　　　　<option value="4月" <?php if($current_month == "4月") echo 'selected'; ?>>4月</option>
　　　　　　　　　　　<option value="5月" <?php if($current_month == "5月") echo 'selected'; ?>>5月</option>
　　　　　　　　　　　<option value="6月" <?php if($current_month == "6月") echo 'selected'; ?>>6月</option>
　　　　　　　　　　　<option value="7月" <?php if($current_month == "7月") echo 'selected'; ?>>7月</option>
　　　　　　　　　　　<option value="8月" <?php if($current_month == "8月") echo 'selected'; ?>>8月</option>
　　　　　　　　　　　<option value="9月" <?php if($current_month == "9月") echo 'selected'; ?>>9月</option>
　　　　　　　　　　　<option value="10月" <?php if($current_month == "10月") echo 'selected'; ?>>10月</option>
　　　　　　　　　　　<option value="11月" <?php if($current_month == "11月") echo 'selected'; ?>>11月</option>
　　　　　　　　　　　<option value="12月" <?php if($current_month == "12月") echo 'selected'; ?>>12月</option>
　　　　　　　　　</select>
　　　　　　  　<input type="text" name="price" placeholder="¥">
　　　　　　    <p class="area-msg"><?php if(!empty($err_msg['price'])) echo '※'.$err_msg['price']; ?></p>
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