<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Xcx extends Controller {
	public function index() {
		$opt = Request::instance()->get('opt');
		$type = Request::instance()->get('type');
		switch ($opt) {
		case 'taobaoTbkDgItemCouponGet':
			$this->get_coupon();
			break;

		default:
			echo "参数不全";
			break;
		}
	}

	// 通过淘宝接口获取优惠券
	public function get_coupon() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		$keyWords = Request::instance()->get('keyWords') ? Request::instance()->get('keyWords') : "女装";
		$PageNo = Request::instance()->get('PageNo') ? Request::instance()->get('PageNo') : 1;
		$type = Request::instance()->post('type');
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkDgItemCouponGetRequest;
		$req->setAdzoneId("42738709");
// $req->setPlatform("1");
		// $req->setCat("16,18");
		$req->setPageSize("20");
		$req->setQ($keyWords);
		$req->setPageNo($PageNo);
		$resp = $c->execute($req);
/*var_dump($resp);exit;
// for($i=0;$i<length($resp.results[tbk_coupon);$i++){

// }*/
		exit(json_encode($resp));
	}
	//根据xls获取淘宝数据
	public function get_coupon_xls() {
		$num = Request::instance()->post('num') ? Request::instance()->post('num') : 20;
		$page = Request::instance()->post('page') ? Request::instance()->post('page') : 0;
		$data = Db::table('tbk_xls')->page($page)->limit($num)->select();
		exit(json_encode($data));
	}
}
