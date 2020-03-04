<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　データ追加ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

?>

<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/data_add.css">
   </head>
   <body>
       <header>
           <h1><a href="index.php">D.vidulum</a></h1>
           
          <!-- ナビゲーション -->
           <nav>
             <ul>
               <li><a href="index.php">ホーム</a></li>
               <li><a href="logout.php">ログアウト</a></li>
             </ul>
           </nav>
       </header>
       <main class="main">
         
          <div class="data-content">
              <p>各種データ追加項目</p>
              <ul>
                  <li><a href="data-savings.php">貯金目標入力</a></li>
                  <li><a href="data-spending.php">出費額を入力する</a></li>
                  <li><a href="data-income.php">収入額を入力する</a></li>
              </ul>
          </div>
         
       </main>
       <section class="backpage">
           <p><a href="mypage.php">&lt;マイページへ戻る</a></p>
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