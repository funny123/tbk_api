<?php

namespace app\admin\controller;


use app\admin\model\OrderModel;
use think\Controller;

class Order extends Controller
{

    /**
     * [index 所有订单列表]
     */
    public function index(){

        $map = '';
        $order = new OrderModel();
        $list = $order->getAllOrder($map);
        $this->assign('list',$list);
        return $this->fetch();
    }

    //查询所有交易中订单
    public function paying()
    {
        $map = ['o.status'=>2];//买单
        $order = new OrderModel();
        $list = $order->getAllOrder($map);
        $this->assign('list',$list);
        return $this->fetch();
    }

    //查询所有买单
    public function buy()
    {
        $map = ['type'=>2];//买单
        $order = new OrderModel();
        $list = $order->getAllOrder($map);
        $this->assign('list',$list);
        return $this->fetch();
    }

    //查询所有卖
    public function sell()
    {
        $map = ['type'=>1];//卖单
        $order = new OrderModel();
        $list = $order->getAllOrder($map);
        $this->assign('list',$list);
        return $this->fetch();
    }


    /**
     * [del_cate 删除分类]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_order()
    {
        $id = input('param.id');
        $cate = new OrderModel();
        $flag = $cate->delOrder($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}