<?php

namespace app\admin\controller;
use app\admin\model\ArticleModel;
use app\admin\model\ArticleCateModel;
use app\admin\model\MillsCateModel;
use app\admin\model\MillsModel;
use think\Db;

class Mills extends Base
{

    /**
     * [index 矿机列表]
     */
    public function index(){

        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['title'] = ['like',"%" . $key . "%"];
        }
        $page = input('get.page') ? input('get.page'):1;
        $rows = input('get.rows');// 获取总条数
        $count = Db::name('mills')->where($map)->count();//计算总页面
        $mills = new MillsModel();
        $lists = $mills->getMillsByWhere($map, $page, $rows);
        $data['list'] = $lists;
        $data['count'] = $count;
        $data['page'] = $page;
        if(input('get.page')){
            return json($data);
        }

        return $this->fetch();
    }


    /**
     * [add_article 添加矿机]
     */
    public function add_mills()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $mills = new MillsModel();
            $flag = $mills->insertMills($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $cate = new ArticleCateModel();
        $this->assign('cate',$cate->getAllCate());
        return $this->fetch();
    }


    /*
     * [edit_article 编辑矿机]
     */
    public function edit_mills()
    {
        $mills = new MillsModel();
        if(request()->isAjax()){

            $param = input('post.');         
            $flag = $mills->updateMills($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $cate = new MillsCateModel();
        $this->assign('cate',$cate->getAllCate());
        $this->assign('mills',$mills->getOneMills($id));
        return $this->fetch();
    }



    /**
     * [del_article 删除矿机]
     */
    public function del_mills()
    {
        $id = input('param.id');
        $cate = new MillsModel();
        $flag = $cate->delMills($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * [article_state 矿机状态]
     */
    public function article_state()
    {
        $id=input('param.id');
        $status = Db::name('article')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('article')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('article')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }



    //*********************************************分类管理*********************************************//

    /**
     * [index_cate 分类列表]
     */
    public function index_cate(){

        $cate = new MillsCateModel();
        $list = $cate->getAllCate();
        $this->assign('list',$list);
        return $this->fetch();
    }


    /**
     * [add_cate 添加分类]
     */
    public function add_cate()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $cate = new MillsCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        return $this->fetch();
    }


    /**
     * [edit_cate 编辑分类]
     */
    public function edit_cate()
    {
        $cate = new MillsCateModel();

        if(request()->isAjax()){

            $param = input('post.');
            $flag = $cate->editCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign('cate',$cate->getOneCate($id));
        return $this->fetch();
    }


    /**
     * [del_cate 删除分类]
     */
    public function del_cate()
    {
        $id = input('param.id');
        $cate = new MillsCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * [cate_state 分类状态]
     */
    public function cate_state()
    {
        $id=input('param.id');
        $status = Db::name('mills_cate')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('mills_cate')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('mills_cate')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }  

}