<?php
namespace app\api\controller;
use PHPExcel_IOFactory;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller {
	public function index() {
		$a = Db::query('select * from tbk_xls where id=?', ['2']);
		print_r($a);
	}
	/*
		根据xls插入优惠券信息到数据库
	*/
	public function dumpxls() {
		$path = ROOT_PATH . 'public' . DS . 'files'; //找到当前脚本所在路径
		$reader = PHPExcel_IOFactory::createReader('Excel5');
		$startTime = time();
		$PHPExcel = $reader->load($path . "/tb.xls"); // 载入excel文件
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		/** 循环读取每个单元格的数据 */
		Db::query('truncate table tbk_xls');
		for ($row = 2; $row <= $highestRow; $row++) //行号从1开始
		{
			// for ($column = 'A'; $column <= $highestColumm; $column++) //列数是以A列开始
			// {
			// $dataset[''] = $sheet->getCell($column . $row)->getValue();
			$data['id'] = '';
			$data['pId'] = $sheet->getCell('A' . $row)->getValue();
			$data['pName'] = $sheet->getCell('B' . $row)->getValue();
			$data['pPic'] = $sheet->getCell('C' . $row)->getValue();
			$data['pDes'] = $sheet->getCell('D' . $row)->getValue();
			$data['pCat'] = $sheet->getCell('E' . $row)->getValue();
			$data['tbk_link'] = $sheet->getCell('F' . $row)->getValue();
			$data['pPrice'] = $sheet->getCell('G' . $row)->getValue();
			$data['pSales'] = $sheet->getCell('H' . $row)->getValue();
			$data['rate'] = $sheet->getCell('I' . $row)->getValue();
			$data['charges'] = $sheet->getCell('J' . $row)->getValue();
			$data['wangwang'] = $sheet->getCell('K' . $row)->getValue();
			$data['sell_id'] = $sheet->getCell('L' . $row)->getValue();
			$data['shop_name'] = $sheet->getCell('M' . $row)->getValue();
			$data['user_type'] = $sheet->getCell('N' . $row)->getValue();
			$data['coupon_id'] = $sheet->getCell('O' . $row)->getValue();
			$data['coupon_num'] = $sheet->getCell('P' . $row)->getValue();
			$data['coupon_remain'] = $sheet->getCell('Q' . $row)->getValue();
			$data['coupon_info'] = $sheet->getCell('R' . $row)->getValue();
			$data['coupon_start'] = $sheet->getCell('S' . $row)->getValue();
			$data['coupon_end'] = $sheet->getCell('T' . $row)->getValue();
			$data['coupon_link'] = $sheet->getCell('U' . $row)->getValue();
			$data['coupon_tbk_link'] = $sheet->getCell('V' . $row)->getValue();

			if (Db::table('tbk_xls')->insert($data)) {
				echo '插入成功';
				echo "<br/>";
			}
			// }
		}
		$endTime = time();
		$time = $endTime - $startTime;
		$count = $highestRow - 1;
		echo "总共插入" . $count . "条商品数据,花了：" . $time . "秒";
		// exit(json_encode($dataset));
	}
	//根据xls获取淘宝数据
	public function get_coupon_xls() {
		$num = Request::instance()->post('num') ? Request::instance()->post('num') : 30;
		$page = Request::instance()->post('page') ? Request::instance()->post('page') : 0;
		$data = Db::table('tbk_xls')->page($page)->limit($num)->select();
		exit(json_encode($data));
	}
	// 通过淘宝接口获取优惠券
	public function get_coupon() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		$keyword = Request::instance()->post('keyword') ? Request::instance()->post('keyword') : "女装";
		$pageno = Request::instance()->post('pageno') ? Request::instance()->post('pageno') : 1;
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkDgItemCouponGetRequest;
		$req->setAdzoneId("42738709");
// $req->setPlatform("1");
		// $req->setCat("16,18");
		$req->setPageSize("20");
		$req->setQ($keyword);
		$req->setPageNo($pageno);
		$resp = $c->execute($req);
/*var_dump($resp);exit;
// for($i=0;$i<length($resp.results[tbk_coupon);$i++){

// }*/
		exit(json_encode($resp));
	}

	// 获取淘宝联盟选品库列表
	public function get_favorites_list() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		$page_size = Request::instance()->post('page_size') ? Request::instance()->post('page_size') : 30;
		$page_no = Request::instance()->post('page_no') ? Request::instance()->post('page_no') : 1;
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkUatmFavoritesGetRequest;
		$req->setPageNo($page_no);
		$req->setPageSize($page_size);
		$req->setFields("favorites_title,favorites_id,type");
		$req->setType("2");
		$resp = $c->execute($req);
		exit(json_encode($resp));
	}
	// 淘宝客店铺查询
	public function get_tbk_shop() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		$keyword = Request::instance()->post('keyword') ? Request::instance()->post('keyword') : "女装";
		$pageno = Request::instance()->post('pageno') ? Request::instance()->post('pageno') : 1;
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkShopGetRequest;
		$req->setFields("user_id,shop_title,shop_type,seller_nick,pict_url,shop_url");
		$req->setQ($keyword);
		$req->setSort("commission_rate_des");
		$req->setIsTmall("true");
		$req->setStartCredit("20");
		// $req->setEndCredit("20");
		// $req->setStartCommissionRate("2000");
		// $req->setEndCommissionRate("123");
		// $req->setStartTotalAction("1");
		// $req->setEndTotalAction("100");
		// $req->setStartAuctionCount("123");
		$req->setEndAuctionCount("200");
		// $req->setPlatform("1");
		// $req->setPageNo("1");
		// $req->setPageSize("20");
		$resp = $c->execute($req);
		exit(json_encode($resp));
	}
	// 获取淘宝联盟选品库的宝贝信息
	public function get_favorites_info() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		$keyword = Request::instance()->post('keyword') ? Request::instance()->post('keyword') : "女装";
		$pageno = Request::instance()->post('pageno') ? Request::instance()->post('pageno') : 1;
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkUatmFavoritesItemGetRequest;
		$req->setPlatform("1");
		$req->setPageSize("20");
		$req->setAdzoneId("630212900");
		$req->setUnid("3456");
		$req->setFavoritesId("17491461");
		$req->setPageNo($pageno);
		$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,click_url,coupon_click_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
		$resp = $c->execute($req);
		exit(json_encode($resp));
	}
	// 淘宝客淘口令
	public function tbwd_create() {
		include EXTEND_PATH . 'taobao/TopSdk.php';
		$appkey = "21781101";
		$secret = "425355d23269cef324dd6da471bbbba0";
		// $text = Request::instance()->post('text') ? Request::instance()->post('text') : "女装";
		// $url = Request::instance()->post('url') ? Request::instance()->post('url') : "https://uland.taobao.com/coupon/edetail?e=AF4UgQhblOsN%2BoQUE6FNzCIKg5MyRqeu3jxL4yynuHMw5hF5QJa7vRpBwAz9u2FrZLLvuBaD3O0gvTdfFk7ReB0HgBdG%2FDDL%2F1M%2FBw7Sf%2FcWnbFS%2FC9hckSR178x1qA3q3zE3YYH54YO4DWmnRwbehBgND0Eu0%2Fy&af=1&pid=mm_15650374_24070258_80360885";
		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new \TbkTpwdCreateRequest;
		$req->setUserId("mafan12390");
		$req->setText("长度大于5个字符");
		$req->setUrl("https://uland.taobao.com/");
		// $req->setLogo("https://uland.taobao.com/");
		// $req->setExt("{}");
		$resp = $c->execute($req);
		exit(json_encode($resp));
	}
}
