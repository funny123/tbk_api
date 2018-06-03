<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Index extends Controller {
	/**
	 * 后台首页
	 * @return [type] [description]
	 */
	public function index() {
		$isAdmin = $this->checkAdmin();
		if (!$isAdmin) {
			return $this->fetch();
		} else {
			return 'no';
		}

	}
	private function checkAdmin() {
		$isAdmin = Session::get('admin') ? 1 : 0;
		return $isAdmin;
	}
}
