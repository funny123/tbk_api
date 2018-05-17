<?php
include "TopSdk.php";
$appkey = "21781101";
$secret = "425355d23269cef324dd6da471bbbba0";
function object_array($array) {
	if (is_object($array)) {
		$array = (array) $array;
	}
	if (is_array($array)) {
		foreach ($array as $key => $value) {
			$array[$key] = object_array($value);
		}
	}
	return $array;
}
$c = new TopClient;
$c->appkey = $appkey;
$c->secretKey = $secret;
$req1 = new TbkItemGetRequest;
$req1->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
$req1->setQ("女装");
// $req->setCat("16,18");
// $req->setItemloc("杭州");
$req1->setSort("total_sales_des");
// $req->setIsTmall("false");
// $req->setIsOverseas("false");
$req1->setStartPrice("5");
$req1->setEndPrice("10");
$req1->setStartTkRate("500");
$req1->setEndTkRate("1500");
// $req1->setPlatform("2");
// $req1->setPageNo($PageNo);
// $req1->setPageSize("20");
$resp1 = $c->execute($req1);
$arr_result = object_array($resp1);
$arr_iid = $arr_result['results']['n_tbk_item'];
foreach ($arr_iid as $key => $value) {
	$num_iid[$key] = $value['num_iid'];
}

// print_r($num_iid);exit;
$iid = join(',', $num_iid);
$req2 = new TbkItemInfoGetRequest;
$req2->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
$req2->setPlatform("1");
$req2->setNumIids($iid);
$resp2 = $c->execute($req2);
exit(json_encode($resp2));
?>