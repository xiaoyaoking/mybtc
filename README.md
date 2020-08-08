# showmybtc
show btc info

优点:冷钱包监视,隐藏钱包地址. 自定义显示货币. 自定义显示时间内的交易.
缺点:需要放到海外vps,因为获取数据的站点已经被墙.

配置信息

{

	"title": "My Btc", //显示标题
	
	"password":"123", //暂时未添加
	
	"currency": "CNY", //当前法币
	
	"currencylist":["USD","CNY"], //显示的法币汇率
	
	"showtime":"a", // 显示的时间段  a:全部 y:本年 m:本月 d:本日
	
	"btc": [ //btc地址列表
	
		{
		
			"name": "小金库", //显示名称
			
			"key": "1JNwRa9SVHYtakuyAhAi48h5wSEWRncg8Z" // btc地址
			
		},
		
		{
		
			"name": "大宝库", //显示名称
			
			"key": "3E8ociqZa9mZUSwGdSmAEMAoAxBK3FNDcd" // btc地址
			
		}
		
	]
	
}

