<?php
/*
Name:Show My Btc
Url:https://github.com/xiaoyaoking/showmybtc
Time:2020/08/08
*/
$c = json_decode(file_get_contents("c.json"),1);
$title = $c["title"];
$currency = $c["currency"];
$addr = $c["btc"];
$currencylist = $c["currencylist"];
$showtime = $c["showtime"];

$infoUrl = "https://blockchain.info/multiaddr?cors=true&active=";
$infoUrl2 ="https://api.blockcypher.com/v1/btc/main/addrs/";
$tickerUrl ="https://blockchain.info/ticker?base=BTC"; 
$header = array(
	"accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
	"accept-language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7",
	"user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36"
);
$get_currency = get_currency();
$ret=http_req($tickerUrl,$header,'');
$currency_list = $get_currency['currency_list'];
$currency_list_show = $get_currency['currency_list_show'];
$currency_val = $currency_list[$currency]['last'];
$currency_symbol = $currency_list[$currency]['symbol'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $title;?></title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="style/css/layui.css"  media="all">
  <style>.text-success {color: #28a745!important;}.text-danger {color: #dc3545!important;}</style>
</head>
<body>
              
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
  <legend><?php echo $title;?></legend>
</fieldset>
 
 
<div class="layui-tab">
<div class="layui-tab-content">
当前汇率:
	 <?php foreach($currency_list_show as $key=>$val){ ?>
	<span class="layui-badge-rim"><?php echo $key.':'.$val['last'];?></span>
	<?php } ?>
</div>
  <ul class="layui-tab-title">
  <?php
  $i=0;
  foreach($addr as $item){
	 ?>
	<li<?php if($i==0){echo ' class="layui-this"';}?>><?php echo $item['name'];?></li>
	<?php $i++;}?>
  </ul>
  <div class="layui-tab-content">
   <?php
  $i=0;
  foreach($addr as $item){
	$ret=http_req($infoUrl.$item['key'],$header,'');
	$infolist = init_data1($ret[1],$currency_val);
?>
	<div class="layui-tab-item<?php if($i==0){echo ' layui-show';}?>">
	<span class="layui-badge-rim">余额:<?php echo $infolist['final_balance'];?></span>
	<span class="layui-badge layui-bg-green">赚到:<?php echo $infolist['total_received'];?></span>
	<span class="layui-badge">花了:<?php echo $infolist['total_sent'];?></span>
	 <table class="layui-table">
    <colgroup>
      <col width="16">
      <col width="150">
      <col width="200">
      <col>
    </colgroup>
    <thead>
      <tr>
		<th></th>
        <th>时间</th>
        <th>金额</th>
        <th>法币(<?php echo $currency_symbol;?>)</th>
      </tr> 
    </thead>
    <tbody>
	<?php foreach($infolist['list'] as $key=>$val){?>
      <tr>
		<td><img src="style/img/<?php echo $val['icon'] ?>.png" alt="Confs" width="16" height="16"></td>
        <td><?php echo $val['date'] ?></td>
        <td class="<?php echo $val['color'] ?>"><?php echo $val['amount'] ?></td>
        <td><?php echo $val['money'] ?></td>
      </tr>
	<?php } ?>
    </tbody>
  </table>
	</div>
	<?php $i++;}?>
  </div>
</div>
<script src="style/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
layui.use('element', function(){
  $ = layui.jquery
  ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

  $('.site-demo-active').on('click', function(){
    $othis = $(this), type = othis.data('type');
    active[type] ? active[type].call(this, othis) : '';
  });

});
</script>

</body>
</html>

<?php
function init_data1($jasostr,$course){ //blockchain.com
	global $showtime;
	$jsonval = json_decode($jasostr,1);
	$total_received = format($jsonval['wallet']['total_received']);
	$total_sent = format($jsonval['wallet']['total_sent']);
	$final_balance = format($jsonval['wallet']['final_balance']);
	$n_tx = $jsonval['wallet']['n_tx'];
	$n = count($jsonval['txs']);
	$list = array();
	$nowtime = date("Y-m-d",time());
	
	for($i=0; $i<$n; $i++) {
		$isshow = false;
		$date = date("Y-m-d H:i",$jsonval['txs'][$i]['time']);
		if($showtime=="a"){
			$isshow = true;
		}
		if($showtime=="y"){
			if(date("Y",$jsonval['txs'][$i]['time'])==date("Y",time())){
				$isshow = true;
			}
		}
		if($showtime=="m"){
			if(date("Y-m",$jsonval['txs'][$i]['time'])==date("Y-m",time())){
				$isshow = true;
			}
		}
		if($showtime=="d"){
			if(date("Y-m-d",$jsonval['txs'][$i]['time'])==date("Y-m-d",time())){
				$isshow = true;
			}
		}
		if($isshow){
			$color = ($jsonval['txs'][$i]['result'] < 0 ? 'text-danger' : 'text-success');
			$conf = 0;
			if($jsonval['txs'][$i]['block_height'] != null) $conf = $jsonval['info']['latest_block']['height'] - $jsonval['txs'][$i]['block_height'] + 1;
			if($conf < 6) {
				$icon = '0';
			} else {
				$icon = '1';
			}
			$fee = round(($jsonval['txs'][$i]['fee'] / ($jsonval['txs'][$i]['weight'] / 4)),2);
			$money = round(abs(format($jsonval['txs'][$i]['result'])) * $course,2);
			$amount = format($jsonval['txs'][$i]['result']);
			$list[]=array("amount"=>$amount,"date"=>$date,"fee"=>$fee,"money"=>$money,"amount"=>$amount,"conf"=>$conf,"icon"=>$icon,"color"=>$color);
		}
	}
	return array("total_received"=>$total_received,"total_sent"=>$total_sent,"final_balance"=>$final_balance,"n_tx"=>$n_tx,"list"=>$list);
}
function get_currency(){
	global $tickerUrl,$header,$currencylist;
	$ret=http_req($tickerUrl,$header,'');
	$currency_list = json_decode($ret[1],1);
	$currency_val = $currency_list[$currency]['last'];
	$currency_symbol = $currency_list[$currency]['symbol'];
	$currency_list_show = array();
	foreach($currency_list as $key=>$val){
		if(in_array($key,$currencylist)){
			$currency_list_show[$key]=$val;
		}
	}
	return array("currency_list"=>$currency_list,"currency_list_show"=>$currency_list_show);
}
function format($number) {
	//$number = $number / 100000000;
	$number = bcdiv($number, 100000000, 8);
	$point = strpos($number,".");
	if($point != -1) {
		$after = substr($number,$point+1);
		$after = strlen($after);
		$after = 8-$after;
		for($i=0;$i<$after;$i++) {
			$number .= "0";
		}
	} else {
		$number .= ".00000000";
	}
	return($number);
}
function http_req($url,$headers,$post_data){  
    $ch = curl_init($url) ;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
    curl_setopt($ch, CURLOPT_TIMEOUT, 90) ;
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0) ;

    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if(!empty($post_data)){
		$post_data=str_replace('null','',$post_data);
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }else{
		curl_setopt($ch, CURLOPT_HTTPGET, 1);//get提交方式
	}

    curl_setopt($ch, CURLOPT_ENCODING, "gzip"); 

    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLINFO_HEADER_OUT , $headers);
    
    //curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'readHeader');
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if(empty($response)){
        echo "curl_error:".$url;
        var_dump( curl_error($ch));
    }
    // Then, after your curl_exec call:
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    return array($header,$body,$httpcode);
}
?>
