<?php

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//右横のいろいろな金額を計算する
//当月の収入額
$income_month = income_month($current_year,$current_month);
//今年の収入額
$income_year = income_year($current_year);
//今年の目標貯金額
$savings_value = saving_get($current_year);
//今年の出費額
$spend_year = spend_year($current_year);
//今月の出費額
$spend_month = spend_month($current_year,$current_month);
//今年の貯金達成金額
$achieve_value = ($income_year - $spend_year);
//今年の貯金額達成割合
$achieve_per = (int)(($achieve_value / $savings_value)*100);
//今月の可処分所得残金
$usable_value = (int)($income_month - (($savings_value / 12) + $spend_month));


//各POST送信するデータを変数に格納する
  //円グラフ
  $circle_seireki = $_POST['circle_seireki'];
  $circle_month = $_POST['circle_month'];
  //支出額一覧
  $spend_seireki = $_POST['spend_seireki'];
  
  $switch_msg = array($circle_seireki,$spend_seireki);


//支出額一覧でPOST送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります。');
    
    if(!empty($_POST['circle_seireki']) && !empty($_POST['circle_month'])){
        
        $circle_table = array();
        $p_type = array('水道代','電気代','ガス代','住居代','携帯代','交通費','食費','娯楽費','自己投資','その他');
        
        for($i=0; $i<10; $i++){
            $dbh = dbConnect();
            $sql = switch_case();
            $data = array(':year' => $circle_seireki, ':month' => $circle_month, ':p_type' => $p_type[$i], ':user_id' => $_SESSION['user_id']);
        
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($result['SUM(price)'])){
                $circle_table[] = 0;
            }else{
                $circle_table[] = $result['SUM(price)'];
            }
        }
        
    }else{
        
    }
    
    if(!empty($_POST['spend_seireki'])){
        
        $spend_table = array();
        $p_type = array('水道代','電気代','ガス代','住居代','携帯代','交通費','食費','娯楽費','自己投資','その他');
        $month = array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
        
        
        for($p=0; $p<10; $p++){
            
            for($m=0; $m<12; $m++){
                $dbh = dbConnect();
                $sql = switch_case();
                $data = array(':year' => $spend_seireki, ':month' => $month[$m], ':p_type' => $p_type[$p], ':user_id' => $_SESSION['user_id']);
        
                $stmt = queryPost($dbh,$sql,$data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $spend_table[] = $result['SUM(price)'];
            }
        }
    }    
}else{
    debug('POST送信はありません');
}
?>


<!DOCTYPE html>
<html lang="ja">
   <head>
       <meta charset="utf-8">
       <title>webサービス作成1</title>
       <link rel="stylesheet" href="css/common.css">
       <link rel="stylesheet" href="css/mypage.css">
       <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js" type="text/javascript"></script>
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
        <div class="container-container">
    
        <!-- 円グラフ -->
         <div class="form-container">
           <p class="title"><b>支出分布グラフ</b></p>
           <form action="" method="post" class="form">
             <select name="circle_seireki" class="form-content">
                  <option value="disabled" style="display:none;" >選択して下さい</option>
　　　　        　　<option value="2018年" <?php if($_POST['circle_seireki'] == "2018年") echo 'selected'; ?>>2018年</option>
                  <option value="2019年" <?php if($_POST['circle_seireki'] == "2019年") echo 'selected'; ?>>2019年</option>
                  <option value="2020年" <?php if($_POST['circle_seireki'] == "2020年") echo 'selected'; ?>>2020年</option>
                 
             </select>
　　　　　　　　<select name="circle_month" class="form-content">
　　　　　　　　     <option value="disabled" style="display:none;" >選択して下さい</option>
　　　　　　　　　　　<option value="1月" <?php if($_POST['circle_month'] == "1月") echo 'selected'; ?>>1月</option>
　　　　　　　　　　　<option value="2月" <?php if($_POST['circle_month'] == "2月") echo 'selected'; ?>>2月</option>
　　　　　　　　　　　<option value="3月" <?php if($_POST['circle_month'] == "3月") echo 'selected'; ?>>3月</option>
　　　　　　　　　　　<option value="4月" <?php if($_POST['circle_month'] == "4月") echo 'selected'; ?>>4月</option>
　　　　　　　　　　　<option value="5月" <?php if($_POST['circle_month'] == "5月") echo 'selected'; ?>>5月</option>
　　　　　　　　　　　<option value="6月" <?php if($_POST['circle_month'] == "6月") echo 'selected'; ?>>6月</option>
　　　　　　　　　　　<option value="7月" <?php if($_POST['circle_month'] == "7月") echo 'selected'; ?>>7月</option>
　　　　　　　　　　　<option value="8月" <?php if($_POST['circle_month'] == "8月") echo 'selected'; ?>>8月</option>
　　　　　　　　　　　<option value="9月" <?php if($_POST['circle_month'] == "9月") echo 'selected'; ?>>9月</option>
　　　　　　　　　　　<option value="10月" <?php if($_POST['circle_month'] == "10月") echo 'selected'; ?>>10月</option>
　　　　　　　　　　　<option value="11月" <?php if($_POST['circle_month'] == "11月") echo 'selected'; ?>>11月</option>
　　　　　　　　　　　<option value="12月" <?php if($_POST['circle_month'] == "12月") echo 'selected'; ?>>12月</option>
　　　　　　　　　</select>
　　　　　　  　<input type="submit" value="表示する" class="form-content">
　　　　　　  </form>
　　　　　　  <div id="text" class="notcircle-text">
　　　　　　    <canvas id="pie-chart" height="380" width="550"></canvas>
　　　　　　    <input  id="water-price" type="hidden" value="<?php echo ($circle_table[0]); ?>">
　　　　　　  　<input  id="ele-price" type="hidden" value="<?php echo ($circle_table[1]); ?>">
　　　　　　  　<input  id="gas-price" type="hidden" value="<?php echo ($circle_table[2]); ?>">
　　　　　　  　<input  id="house-price" type="hidden" value="<?php echo ($circle_table[3]); ?>">
　　　　　　  　<input  id="phone-price" type="hidden" value="<?php echo ($circle_table[4]); ?>">
　　　　　　  　<input  id="move-price" type="hidden" value="<?php echo ($circle_table[5]); ?>">
　　　　　　  　<input  id="food-price" type="hidden" value="<?php echo ($circle_table[6]); ?>">
　　　　　　  　<input  id="enjoy-price" type="hidden" value="<?php echo ($circle_table[7]); ?>">
　　　　　　  　<input  id="self-price" type="hidden" value="<?php echo ($circle_table[8]); ?>">
　　　　　　  　<input  id="other-price" type="hidden" value="<?php echo ($circle_table[9]); ?>">
　　　　　　  </div>
　　　　　　  
         </div>
        
        <!-- 金額表示 -->
         <div class="maney-container">
           <ul>
               <li><?php echo $current_year;?>の収入額<p>¥<?php echo $income_year; ?></p></li>
               <li><?php echo $current_month;?>の収入額<p>¥<?php echo $income_month; ?></p></li>
               <li><?php echo $current_month;?>の支出額<p>¥<?php echo $spend_month; ?></p></li>
               <li><?php echo $current_month;?>の支出額の可処分残額<p>¥<?php echo $usable_value; ?></p></li>
               <li><?php echo $current_year;?>貯金達成金額<p>¥<?php echo $achieve_value; ?></p></li>
               <li><?php echo $current_year;?>貯金達成率<p><?php echo $achieve_per; ?>%</p></li>
           </ul>
         </div>
        </div>
        
        <!-- 支出額一覧 -->
        <div class="table-a-container">
           <p class="title"><b>支出額一覧</b></p>
           <form action="" method="post" class="form">
             <select name="spend_seireki" class="form-content">
                  <option value="disabled" style="display:none;" >選択してください</option>
　　　　        　　<option value="2018年" <?php if($_POST['spend_seireki'] == "2018年") echo 'selected'; ?>>2018年</option>
                  <option value="2019年" <?php if($_POST['spend_seireki'] == "2019年") echo 'selected'; ?>>2019年</option>
                  <option value="2020年" <?php if($_POST['spend_seireki'] == "2020年") echo 'selected'; ?>>2020年</option>
                 
             </select>
             <input type="submit" value="表示する" class="form-content">
　　　　　　  </form>
            <table cellspacing="3">
                
              <thead>
                <tr><th>支出項目/月</th><th>1月</th><th>2月</th><th>3月</th><th>4月</th><th>5月</th><th>6月</th><th>7月</th><th>8月</th><th>9月</th><th>10月</th><th>11月</th><th>12月</th><th>平均</th><th>合計</th></tr>
              </thead>
              <tbody class="td_right">
                <tr>
                  <th>水道代</th>
                  <td><?php calldata(0); ?></td><td><?php calldata(1); ?></td><td><?php calldata(2); ?></td>
                  <td><?php calldata(3); ?></td><td><?php calldata(4); ?></td><td><?php calldata(5); ?></td>
                  <td><?php calldata(6); ?></td><td><?php calldata(7); ?></td><td><?php calldata(8); ?></td>
                  <td><?php calldata(9); ?></td><td><?php calldata(10); ?></td><td><?php calldata(11); ?></td>
                  <td><?php echo $w_avg = callavg(0,11); ?></td><td><?php callsum(0,11); ?></td>
                </tr>
    
                <tr>
                  <th>電気代</th>
                  <td><?php calldata(12); ?></td><td><?php calldata(13); ?></td><td><?php calldata(14); ?></td>
                  <td><?php calldata(15); ?></td><td><?php calldata(16); ?></td><td><?php calldata(17); ?></td>
                  <td><?php calldata(18); ?></td><td><?php calldata(19); ?></td><td><?php calldata(20); ?></td>
                  <td><?php calldata(21); ?></td><td><?php calldata(22); ?></td><td><?php calldata(23); ?></td>
                  <td><?php echo $ele_avg = callavg(12,23); ?></td><td><?php callsum(12,23); ?></td>
                </tr>
    
                <tr>
                  <th>ガス代</th>
                  <td><?php calldata(24); ?></td><td><?php calldata(25); ?></td><td><?php calldata(26); ?></td>
                  <td><?php calldata(27); ?></td><td><?php calldata(28); ?></td><td><?php calldata(29); ?></td>
                  <td><?php calldata(30); ?></td><td><?php calldata(31); ?></td><td><?php calldata(32); ?></td>
                  <td><?php calldata(33); ?></td><td><?php calldata(34); ?></td><td><?php calldata(35); ?></td>
                  <td><?php echo $g_avg = callavg(24,35); ?></td><td><?php callsum(24,35); ?></td>
                </tr>
                
                <tr>
                  <th>住居代</th>
                  <td><?php calldata(36); ?></td><td><?php calldata(37); ?></td><td><?php calldata(38); ?></td>
                  <td><?php calldata(39); ?></td><td><?php calldata(40); ?></td><td><?php calldata(41); ?></td>
                  <td><?php calldata(42); ?></td><td><?php calldata(43); ?></td><td><?php calldata(44); ?></td>
                  <td><?php calldata(45); ?></td><td><?php calldata(46); ?></td><td><?php calldata(47); ?></td>
                  <td><?php echo $h_avg = callavg(36,47); ?></td><td><?php callsum(36,47); ?></td>
                </tr>
    
                <tr>
                  <th>携帯代</th>
                  <td><?php calldata(48); ?></td><td><?php calldata(49); ?></td><td><?php calldata(50); ?></td>
                  <td><?php calldata(51); ?></td><td><?php calldata(52); ?></td><td><?php calldata(53); ?></td>
                  <td><?php calldata(54); ?></td><td><?php calldata(55); ?></td><td><?php calldata(56); ?></td>
                  <td><?php calldata(57); ?></td><td><?php calldata(58); ?></td><td><?php calldata(59); ?></td>
                  <td><?php echo $p_avg = callavg(48,59); ?></td><td><?php callsum(48,59); ?></td>
                </tr>
    
                <tr>
                  <th>交通費</th>
                  <td><?php calldata(60); ?></td><td><?php calldata(61); ?></td><td><?php calldata(62); ?></td>
                  <td><?php calldata(63); ?></td><td><?php calldata(64); ?></td><td><?php calldata(65); ?></td>
                  <td><?php calldata(66); ?></td><td><?php calldata(67); ?></td><td><?php calldata(68); ?></td>
                  <td><?php calldata(69); ?></td><td><?php calldata(70); ?></td><td><?php calldata(71); ?></td>
                  <td><?php echo $t_avg = callavg(60,71); ?></td><td><?php callsum(60,71); ?></td>
                </tr>
                
                <tr>
                  <th>食費</th>
                  <td><?php calldata(72); ?></td><td><?php calldata(73); ?></td><td><?php calldata(74); ?></td>
                  <td><?php calldata(75); ?></td><td><?php calldata(76); ?></td><td><?php calldata(77); ?></td>
                  <td><?php calldata(78); ?></td><td><?php calldata(79); ?></td><td><?php calldata(80); ?></td>
                  <td><?php calldata(81); ?></td><td><?php calldata(82); ?></td><td><?php calldata(83); ?></td>
                  <td><?php echo $f_avg = callavg(72,83); ?></td><td><?php callsum(72,83); ?></td>
                </tr>
                
                <tr>
                  <th>娯楽費</th>
                  <td><?php calldata(84); ?></td><td><?php calldata(85); ?></td><td><?php calldata(86); ?></td>
                  <td><?php calldata(87); ?></td><td><?php calldata(88); ?></td><td><?php calldata(89); ?></td>
                  <td><?php calldata(90); ?></td><td><?php calldata(91); ?></td><td><?php calldata(92); ?></td>
                  <td><?php calldata(93); ?></td><td><?php calldata(94); ?></td><td><?php calldata(95); ?></td>
                  <td><?php echo $enj_avg = callavg(84,95); ?></td><td><?php callsum(84,95); ?></td>
                </tr>
    
                <tr>
                  <th>自己投資</th>
                  <td><?php calldata(96); ?></td><td><?php calldata(97); ?></td><td><?php calldata(98); ?></td>
                  <td><?php calldata(99); ?></td><td><?php calldata(100); ?></td><td><?php calldata(101); ?></td>
                  <td><?php calldata(102); ?></td><td><?php calldata(103); ?></td><td><?php calldata(104); ?></td>
                  <td><?php calldata(105); ?></td><td><?php calldata(106); ?></td><td><?php calldata(107); ?></td>
                  <td><?php echo $s_avg = callavg(96,107); ?></td><td><?php callsum(96,107); ?></td>
                </tr>
    
                <tr>
                  <th>その他</th>
                  <td><?php calldata(108); ?></td><td><?php calldata(109); ?></td><td><?php calldata(110); ?></td>
                  <td><?php calldata(111); ?></td><td><?php calldata(112); ?></td><td><?php calldata(113); ?></td>
                  <td><?php calldata(114); ?></td><td><?php calldata(115); ?></td><td><?php calldata(116); ?></td>
                  <td><?php calldata(117); ?></td><td><?php calldata(118); ?></td><td><?php calldata(119); ?></td>
                  <td><?php echo $o_avg = callavg(108,119); ?></td><td><?php callsum(108,119); ?></td>
                </tr>

              </tbody>
              <tfoot class="td_right">
                <tr>
                <th>合計</th>
                <td><?php call_monthsum(0); ?></td><td><?php call_monthsum(1); ?></td><td><?php call_monthsum(2); ?></td>
                <td><?php call_monthsum(3); ?></td><td><?php call_monthsum(4); ?></td><td><?php call_monthsum(5); ?></td>
                <td><?php call_monthsum(6); ?></td><td><?php call_monthsum(7); ?></td><td><?php call_monthsum(8); ?></td>
                <td><?php call_monthsum(9); ?></td><td><?php call_monthsum(10); ?></td><td><?php call_monthsum(11); ?></td>
                <td><?php echo call_avg_sum(); ?></td><td><?php callsum(0,119); ?></td>
                </tr>
              </tfoot>
            </table>
        </div>
        
       </main>
       
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
          
        //円グラフの値
        $(function(){
               var water = document.querySelector('#water-price').value;
               var ele = document.querySelector('#ele-price').value;
               var gas = document.querySelector('#gas-price').value;
               var house = document.querySelector('#house-price').value;
               var phone = document.querySelector('#phone-price').value;
               var move = document.querySelector('#move-price').value;
               var food = document.querySelector('#food-price').value;
               var enjoy = document.querySelector('#enjoy-price').value;
               var self = document.querySelector('#self-price').value;
               var other = document.querySelector('#other-price').value;
               
               if(water == 0 && ele == 0 && gas == 0 && house == 0 && phone == 0 && move == 0 && food == 0 && enjoy == 0 && self == 0 && other == 0){
                   //円グラフの値が全て0だったらテキストを出す
                  $("#text").text('※支出額を入力して下さい');
               }else{
                   var circle_param = [water,ele,gas,house,phone,move,food,enjoy,self,other];
               }

        // ▼グラフのプロパティ
          
  　　　　 var pieData = [
       　 {
         　　value: circle_param[0],            // 値
        　　  color: "#FDB45C",　　　　　// 色
         　　highlight: "#FFC870",// マウスが載った際の色
         　　label: "水道代"        // ラベル
      　　},
      　　{
         　　value: circle_param[1],
         　　color: "#DF01D7",
         　　highlight: "#F781F3",
         　　label: "電気代"
      　　},
      　　{
         　　value: circle_param[2],
         　　color: "#FDB45C",
         　　highlight: "#FFC870",
         　　label: "ガス代"
      　　},
      　　{
         　　value: circle_param[3],
         　　color: "#01DF01",
         　　highlight: "#81F781",
         　　label: "住居代"
      　　},
      　　{
         　　value: circle_param[4],
         　　color: "#01DFD7",
         　　highlight: "#81F7F3",
         　　label: "携帯代"
      　　},
      　　{
         　　value: circle_param[5],
         　　color:"#DF013A",       
         　　highlight: "#F7819F", 
         　　label: "交通費"
      　　},
      　　{
         　　value: circle_param[6],
         　　color: "#0B614B",
         　　highlight: "#01DF74",
         　　label: "食費"
      　　},
      　　{
         　　value: circle_param[7],
         　　color: "#4D5360",
         　　highlight: "#616774",
         　　label: "娯楽費"
      　　},
         {
         　　value: circle_param[8],
         　　color: "#424242",
         　　highlight: "#848484",
         　　label: "自己投資"
      　　},
         {
         　　value: circle_param[9],
         　　color: "#0B0B61",
         　　highlight: "#0404B4",
         　　label: "その他"
      　　}

   　　　　];

   // ▼上記のグラフを描画するための記述
   　　　　window.onload = function(){
      　　　　var ctx = document.getElementById("pie-chart").getContext("2d");
      　　window.myPie = new Chart(ctx).Pie(pieData);
   　　　　};
        });        
          
      </script>
      
   </body>
</html>