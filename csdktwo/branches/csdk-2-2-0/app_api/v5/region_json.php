<?php
require_once('base.php');
/*******    **********************/

$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$kkk='[
	{
		"key":"100"
		,"val":"其他"
		,"children":
		[
				{"101":"其他"}
			]
		}
	,{
		"key":"101"
		,"val":"北京"
		,"children":
		[
				{"101":"东城区"}
				,{"102":"西城区"}
				,{"103":"宣武区"}
				,{"104":"朝阳区"}
				,{"105":"丰台区"}
				,{"106":"石景山"}
				,{"107":"海淀区"}
				,{"108":"门头沟区"}
				,{"109":"房山区"}
				,{"110":"通州区"}
				,{"111":"昌平区"}
				,{"112":"大兴区"}
				,{"113":"怀柔区"}
				,{"114":"平谷区"}
				,{"115":"密云县"}
				,{"116":"延庆县"}
			]
		}
	,{
		"key":"102"
		,"val":"上海"
		,"children":
		[
	{"101":"黄浦区"}
,{"102":"卢湾区"}
,{"103":"徐汇区"}
,{"104":"长宁区"}
,{"105":"静安区"}
,{"106":"普陀区"}
,{"107":"闸北区"}
,{"108":"虹口区"}
,{"109":"杨浦区"}
,{"110":"闵行区"}
,{"111":"宝山区"}
,{"112":"嘉定区"}
,{"113":"浦东新区"}
,{"114":"金山区"}
,{"115":"松江区"}
,{"116":"青浦区"}
,{"117":"南汇区"}
,{"118":"奉贤区"}
,{"119":"崇明县"}

		]
	}
	,{
		"key":"103"
		,"val":"福建"
		,"children":
		[
	{"101":"福州市"}
,{"102":"厦门市"}
,{"103":"莆田市"}
,{"104":"三明市"}
,{"105":"泉州市"}
,{"106":"漳州市"}
,{"107":"南平市"}
,{"108":"龙岩市"}
,{"109":"宁德市"}

		]
	}
	,{
		"key":"104"
		,"val":"天津"
		,"children":
		[	
	{"101":"和平区"}
,{"102":"河东区"}
,{"103":"河西区"}
,{"104":"南开区"}
,{"105":"河北区"}
,{"106":"红桥区"}
,{"107":"塘沽区"}
,{"108":"汉沽区"}
,{"109":"大港区"}
,{"110":"东丽区"}
,{"111":"西青区"}
,{"112":"津南区"}
,{"113":"北辰区"}
,{"114":"武清区"}
,{"115":"宝坻区"}
,{"116":"宁河县"}
,{"117":"静海县"}
,{"118":"蓟县"}

		]
	}
	,{
		"key":"105"
		,"val":"江苏"
		,"children":
		[	
	{"101":"南京市"}
,{"102":"无锡市"}
,{"103":"徐州市"}
,{"104":"常州市"}
,{"105":"苏州市"}
,{"106":"南通市"}
,{"107":"连云港市"}
,{"108":"淮安市"}
,{"109":"盐城市"}
,{"110":"扬州市"}
,{"111":"镇江市"}
,{"112":"泰州市"}
,{"113":"宿迁市"}
		]
	}
	,{
		"key":"106"
		,"val":"云南"
		,"children":
		[
	{"101":"昆明市"}
,{"102":"曲靖市"}
,{"103":"玉溪市"}
,{"104":"保山市"}
,{"105":"昭通市"}
,{"106":"丽江市"}
,{"107":"思茅市"}
,{"108":"临沧市"}
,{"109":"楚雄彝族自治区"}
,{"110":"红河哈尼族彝族自治州"}
,{"111":"文山壮族苗族自治州"}
,{"112":"西双版纳傣族自治州"}
,{"113":"大理白族自治州"}
,{"114":"德宏傣族景颇族自治州"}
,{"115":"怒江傈僳族自治州"}
,{"116":"迪庆藏族自治州"}
		]
	}
	,{
		"key":"107"
		,"val":"宁夏"
		,"children":
		[
	{"101":"银川市"}
,{"102":"石嘴山市"}
,{"103":"吴忠市"}
,{"104":"固原市"}
,{"105":"中卫市"}
		]
	}
	,{
		"key":"108"
		,"val":"山东"
		,"children":
		[
{"101":"济南市"}
,{"102":"青岛市"}
,{"103":"淄博市"}
,{"104":"枣庄市"}
,{"105":"东营市"}
,{"106":"烟台市"}
,{"107":"淮坊市"}
,{"108":"济宁市"}
,{"109":"泰安市"}
,{"110":"威海市"}
,{"111":"日照市"}
,{"112":"莱芜市"}
,{"113":"临沂市"}
,{"114":"德州市"}
,{"115":"聊城市"}
,{"116":"滨州市"}
,{"117":"菏泽市"}
		]
	}
	,{
		"key":"109"
		,"val":"新疆"
		,"children":
		[
{"101":"乌鲁木齐市"}
,{"102":"克拉玛依市"}
,{"103":"吐鲁番地区"}
,{"104":"哈密地区"}
,{"105":"昌吉回族自治州"}
,{"106":"博尔塔拉蒙古自治州"}
,{"107":"克孜勒苏柯尔克孜自治州"}
,{"108":"喀什地区"}
,{"109":"和田地区"}
,{"110":"伊犁哈萨克自治区"}
,{"111":"塔城地区"}
,{"112":"阿勒泰地区"}
,{"113":"石河子市"}
,{"114":"按拉尔市"}
,{"115":"图木舒克市"}
,{"116":"五家渠市"}
		]
	}
	,{
		"key":"110"
		,"val":"台湾"
		,"children":
		[
	{"101":"台北市"}
,{"102":"高雄市"}
,{"103":"基隆市"}
,{"104":"台中市"}
,{"105":"台南市"}
,{"106":"新竹市"}
,{"107":"嘉义市"}
		]
	}
	,{
		"key":"111"
		,"val":"甘肃"
		,"children":
		[
	{"101":"兰州市"}
,{"102":"嘉峪关市"}
,{"103":"金昌市"}
,{"104":"白银市"}
,{"105":"天水市"}
,{"106":"武威市"}
,{"107":"张掖市"}
,{"108":"平凉市"}
,{"109":"酒泉市"}
,{"110":"庆阳市"}
,{"111":"定西市"}
,{"112":"陇南市"}
,{"113":"临夏回族自治区"}
,{"114":"甘南藏族自治州"}
		]
	}
	,{
		"key":"112"
		,"val":"湖北"
		,"children":
		[
	{"101":"武汉市"}
,{"102":"黄石市"}
,{"103":"十堰市"}
,{"104":"宜昌市"}
,{"105":"襄樊市"}
,{"106":"鄂州市"}
,{"107":"荆门市"}
,{"108":"孝感市"}
,{"109":"黄冈市"}
,{"110":"咸宁市"}
,{"111":"随州市"}
,{"112":"恩施土家族苗族自治州"}
,{"113":"仙桃市"}
,{"114":"潜江市"}
,{"115":"天门市"}
,{"116":"神农架林区"}
		]
	}
	,{
		"key":"113"
		,"val":"江西"
		,"children":
		[
	{"101":"南昌市"}
,{"102":"景德镇市"}
,{"103":"萍乡市"}
,{"104":"九江市"}
,{"105":"新余市"}
,{"106":"鹰潭市"}
,{"107":"赣州市"}
,{"108":"吉安市"}
,{"109":"宜春市"}
,{"110":"抚州市"}
,{"111":"上饶市"}
		]
	}
	,{
		"key":"114"
		,"val":"贵州"
		,"children":
		[
	{"101":"贵阳市"}
,{"102":"六盘水市"}
,{"103":"遵义市"}
,{"104":"安顺市"}
,{"105":"铜仁地区"}
,{"106":"黔西南布依族苗族自治州"}
,{"107":"毕节地区"}
,{"108":"黔东南苗族侗族自治州"}
,{"109":"黔南布依族苗族自治州"}
		]
	}
	,{
		"key":"115"
		,"val":"黑龙江"
		,"children":
		[
	{"101":"哈尔滨市"}
,{"102":"齐齐哈尔市"}
,{"103":"鸡西市"}
,{"104":"鹤岗市"}
,{"105":"双鸭山市"}
,{"106":"大庆市"}
,{"107":"伊春市"}
,{"108":"佳木斯市"}
,{"109":"七台河市"}
,{"110":"牡丹江市"}
,{"111":"黑河市"}
,{"112":"绥化市"}
,{"113":"大兴安岭地区"}
		]
	}
	,{
		"key":"116"
		,"val":"山西"
		,"children":
		[
	{"101":"太原市"}
,{"102":"大同市"}
,{"103":"阳泉市"}
,{"104":"长治市"}
,{"105":"晋城市"}
,{"106":"朔州市"}
,{"107":"晋中市"}
,{"108":"运城市"}
,{"109":"忻州市"}
,{"110":"临汾市"}
,{"111":"吕梁市"}
		]
	}
	,{
		"key":"117"
		,"val":"安徽"
		,"children":
		[
	{"101":"合肥市"}
,{"102":"芜湖市"}
,{"103":"蚌埠市"}
,{"104":"淮南市"}
,{"105":"马鞍山市"}
,{"106":"淮北市"}
,{"107":"铜陵市"}
,{"108":"安庆市"}
,{"109":"黄山市"}
,{"110":"滁州市"}
,{"111":"宿州市"}
,{"112":"巢湖市"}
,{"113":"六安市"}
,{"114":"毫州市"}
,{"115":"池州市"}
,{"116":"宣城市"}
		]
	}
	,{
		"key":"118"
		,"val":"重庆"
		,"children":
		[
	{"101":"万州区"}
,{"102":"涪陵区"}
,{"103":"渝中区"}
,{"104":"大渡口区"}
,{"105":"江北区"}
,{"106":"沙坪坝区"}
,{"107":"九龙坡区"}
,{"108":"南岸区"}
,{"109":"北碚区"}
,{"110":"万盛区"}
,{"111":"双桥区"}
,{"112":"渝北区"}
,{"113":"巴南区"}
,{"114":"黔江区"}
,{"115":"长寿区"}
,{"116":"江津区"}
,{"117":"合川区"}
,{"118":"永川区"}
,{"119":"南川区"}
,{"120":"綦江县"}
,{"121":"潼南县"}
,{"122":"铜梁县"}
,{"123":"大足县"}
,{"124":"荣昌县"}
,{"125":"璧山县"}
,{"126":"梁平县"}
,{"127":"城口县"}
,{"128":"丰都县"}
,{"129":"垫江县"}
,{"130":"武隆县"}
,{"131":"忠县"}
,{"132":"开县"}
,{"133":"云阳县"}
,{"134":"奉节县"}
,{"135":"巫山县"}
,{"136":"巫溪县"}
,{"137":"石柱土家族自治县"}
,{"138":"秀山土家族苗族自治县"}
,{"139":"酉阳土家族苗族自治县"}
,{"140":"彭水苗族土家族自治县"}
		]
	}
	,{
		"key":"119"
		,"val":"河南"
		,"children":
		[
	{"101":"郑州市"}
,{"102":"开封市"}
,{"103":"洛阳市"}
,{"104":"平顶山市"}
,{"105":"安阳市"}
,{"106":"鹤壁市"}
,{"107":"新乡市"}
,{"108":"焦作市"}
,{"109":"濮阳市"}
,{"110":"许昌市"}
,{"111":"漯河市"}
,{"112":"三门峡市"}
,{"113":"南阳市"}
,{"114":"商丘市"}
,{"115":"信阳市"}
,{"116":"周口市"}
,{"117":"驻马店市"}
,{"118":"济源市"}
		]
	}
	,{
		"key":"120"
		,"val":"陕西"
		,"children":
		[
	{"101":"中西区"}
,{"102":"湾仔区"}
,{"103":"东区"}
,{"104":"南区"}
,{"105":"油尖旺区"}
,{"106":"深水埗区"}
,{"107":"九龙城区"}
,{"108":"黄大仙区"}
,{"109":"观塘区"}
,{"110":"荃湾区"}
,{"111":"葵青区"}
,{"112":"沙田区"}
,{"113":"西贡区"}
,{"114":"大埔区"}
,{"115":"北区"}
,{"116":"元朗区"}
,{"117":"屯门区"}
,{"118":"离岛区"}
		]
	}
	,{
		"key":"121"
		,"val":"香港"
		,"children":
		[
	{"101":"中西区"}
,{"102":"湾仔区"}
,{"103":"东区"}
,{"104":"南区"}
,{"105":"油尖旺区"}
,{"106":"深水埗区"}
,{"107":"九龙城区"}
,{"108":"黄大仙区"}
,{"109":"观塘区"}
,{"110":"荃湾区"}
,{"111":"葵青区"}
,{"112":"沙田区"}
,{"113":"西贡区"}
,{"114":"大埔区"}
,{"115":"北区"}
,{"116":"元朗区"}
,{"117":"屯门区"}
,{"118":"离岛区"}
		]
	}
	,{
		"key":"122"
		,"val":"湖南"
		,"children":
		[
	{"101":"长沙市"}
,{"102":"株洲市"}
,{"103":"湘潭市"}
,{"104":"衡阳市"}
,{"105":"邵阳市"}
,{"106":"岳阳市"}
,{"107":"常德市"}
,{"108":"张家界市"}
,{"109":"益阳市"}
,{"110":"郴州市"}
,{"111":"永州市"}
,{"112":"怀化市"}
,{"113":"娄底市"}
,{"114":"湘西土家族苗族自治州"}
		]
	}
	,{
		"key":"123"
		,"val":"广东"
		,"children":
		[
	{"101":"广州市"}
,{"102":"韶关市"}
,{"103":"深圳市"}
,{"104":"珠海市"}
,{"105":"汕头市"}
,{"106":"佛山市"}
,{"107":"江门市"}
,{"108":"湛江市"}
,{"109":"茂名市"}
,{"110":"肇庆市"}
,{"111":"惠州市"}
,{"112":"梅州市"}
,{"113":"汕尾市"}
,{"114":"河源市"}
,{"115":"阳江市"}
,{"116":"周口市"}
,{"117":"东莞市"}
,{"118":"中山市"}
,{"119":"潮州市"}
,{"120":"揭阳市"}
,{"121":"云浮市"}
		]
	}
	,{
		"key":"124"
		,"val":"四川"
		,"children":
		[
	{"101":"成都市"}
,{"102":"自贡市"}
,{"103":"攀枝花市"}
,{"104":"泸州市"}
,{"105":"德阳市"}
,{"106":"绵阳市"}
,{"107":"广元市"}
,{"108":"遂宁市"}
,{"109":"内江市"}
,{"110":"乐山市"}
,{"111":"眉山市"}
,{"112":"南充市"}
,{"113":"宜宾市"}
,{"114":"广安市"}
,{"115":"达州市"}
,{"116":"雅安市"}
,{"117":"巴中市"}
,{"118":"资阳市"}
,{"119":"阿坝藏族羌族自治州"}
,{"120":"甘孜藏族自治州"}
,{"121":"凉山彝族自治州"}
		]
	}
	,{
		"key":"125"
		,"val":"吉林"
		,"children":
		[
	{"101":"长春市"}
,{"102":"吉林市"}
,{"103":"辽源市"}
,{"104":"通化市"}
,{"105":"白山市"}
,{"106":"松原市"}
,{"107":"白城市"}
,{"108":"延边朝鲜族自治区"}
		]
	}
	,{
		"key":"126"
		,"val":"浙江"
		,"children":
		[
	{"101":"杭州市"}
,{"102":"宁波市"}
,{"103":"温州市"}
,{"104":"嘉兴市"}
,{"105":"湖州市"}
,{"106":"绍兴市"}
,{"107":"金华市"}
,{"108":"衢州市"}
,{"109":"舟山市"}
,{"110":"台州市"}
,{"111":"丽水市"}
		]
	}
	,{
		"key":"127"
		,"val":"河北"
		,"children":
		[
	{"101":"石家庄市"}
,{"102":"唐山市"}
,{"103":"秦皇岛市"}
,{"104":"邯郸市"}
,{"105":"邢台市"}
,{"106":"保定市"}
,{"107":"张家口市"}
,{"108":"承德市"}
,{"109":"沧州市"}
,{"110":"廊坊市"}
,{"111":"衡水市"}
		]
	}
	,{
		"key":"128"
		,"val":"西藏"
		,"children":
		[
	{"101":"拉萨市"}
,{"102":"昌都地区"}
,{"103":"山南地区"}
,{"104":"日喀则地区"}
,{"105":"那曲地区"}
,{"106":"阿里地区"}
,{"107":"林芝地区"}
		]
	}
	,{
		"key":"129"
		,"val":"海南"
		,"children":
		[
	{"101":"海口市"}
,{"102":"三亚市"}
,{"103":"五指山市"}
,{"104":"琼海市"}
,{"105":"儋州市"}
,{"106":"文昌市"}
,{"107":"万宁市"}
,{"108":"东方市"}
,{"109":"定安县"}
,{"110":"屯昌县"}
,{"111":"澄迈县"}
,{"112":"临高县"}
,{"113":"白沙县"}
,{"114":"昌江县"}
,{"115":"乐东县"}
,{"116":"陵水县"}
,{"117":"保亭县"}
,{"118":"琼中县"}
,{"119":"洋浦县"}
		]
	}
	,{
		"key":"130"
		,"val":"广西"
		,"children":
		[
	{"101":"南宁市"}
,{"102":"柳州市"}
,{"103":"桂林市"}
,{"104":"琼海市"}
,{"105":"北海市"}
,{"106":"防城港市"}
,{"107":"钦州市"}
,{"108":"贵港市"}
,{"109":"定安县"}
,{"110":"百色市"}
,{"111":"贺州市"}
,{"112":"河池市"}
,{"113":"来宾市"}
,{"114":"崇左市"}
		]
	}
	,{
		"key":"131"
		,"val":"内蒙古"
		,"children":
		[
	{"101":"呼和浩特市"}
,{"102":"包头市"}
,{"103":"乌海市"}
,{"104":"赤峰市"}
,{"105":"通辽市"}
,{"106":"鄂尔多斯市"}
,{"107":"呼伦贝尔市"}
,{"108":"巴彦淖尔市"}
,{"109":"乌兰察布市城县"}
,{"110":"兴安盟"}
,{"111":"锡林郭勒盟"}
,{"112":"阿拉善盟"}
		]
	}
	,{
		"key":"132"
		,"val":"辽宁"
		,"children":
		[
	{"101":"沈阳市"}
,{"102":"大连市"}
,{"103":"鞍山市"}
,{"104":"抚顺市"}
,{"105":"本溪市"}
,{"106":"丹东市"}
,{"107":"锦州市"}
,{"108":"营口市"}
,{"109":"阜新市"}
,{"110":"辽阳市"}
,{"111":"盘锦市"}
,{"112":"铁岭市"}
,{"113":"朝阳市"}
,{"114":"葫芦岛市"}
		]
	}
	,{
		"key":"133"
		,"val":"青海"
		,"children":
		[
	{"101":"西宁市"}
,{"102":"海东地区"}
,{"103":"海北藏族自治州"}
,{"104":"黄南藏族自治州"}
,{"105":"海南藏族自治州"}
,{"106":"果洛藏族自治州"}
,{"107":"玉树藏族自治州"}
,{"108":"海西蒙古族藏族自治州"}
		]
	}
]
';
$a=json_decode($kkk);

$sql="delete from cixi_region_conf";
$res_del =  PdbcTemplate::execute($sql);


foreach ($a as $k => $v) {
	if ($v->key>100) {
		$province_id=$v->key;
		$province_name=$v->val;
		$region_level=2;
		$pid=1;
		$sql="insert into cixi_region_conf(id,pid,name,region_level) values
			($province_id,$pid,'$province_name',$region_level)";
		$res_insert_p =  PdbcTemplate::execute($sql);
		
		//var_dump($sql);

		$children=$v->children;
		foreach ($children as $kc => $vc) {
			
			$city_id=$kc+101;
			$city_name=$vc->$city_id;
			$region_level=3;
			$pid=$province_id;
			$sql="insert into cixi_region_conf(id,pid,name,region_level) values
			($city_id,$pid,'$city_name',$region_level)";
			$res_insert_c =  PdbcTemplate::execute($sql);

		}
	}
	
}
if ($res_del[0]&&$res_insert_c[0]&&$res_insert_p[0]) {

	echo "更新地区表成功!";
}else{
	echo "更新地区表失败!";
}



//$sql="delete from cixi_region_conf";






?>

