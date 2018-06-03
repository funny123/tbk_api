<?php

namespace app\admin\controller;
use app\admin\model\ArticleModel;
use app\admin\model\ArticleCateModel;
use think\Db;

class Article extends Base
{

    /**
     * [index 文章列表]
     * @author [田建龙] [864491238@qq.com]
     */
    public function index(){

        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['title'] = ['like',"%" . $key . "%"];          
        }       
        $page = input('get.page') ? input('get.page'):1;
        $rows = input('get.rows');// 获取总条数
        $count = Db::name('article')->where($map)->count();//计算总页面
        $article = new ArticleModel();
        $lists = $article->getArticleByWhere($map, $page, $rows);
        $data['list'] = $lists;
        $data['count'] = $count;
        $data['page'] = $page;
        if(input('get.page')){
            return json($data);
        }
        return $this->fetch();
    }


    /**
     * [add_article 添加文章]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function add_article()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $article = new ArticleModel();
            $flag = $article->insertArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $cate = new ArticleCateModel();
        $this->assign('cate',$cate->getAllCate());
        return $this->fetch();
    }


    /**
     * [edit_article 编辑文章]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function edit_article()
    {
        $article = new ArticleModel();     
        if(request()->isAjax()){

            $param = input('post.');         
            $flag = $article->updateArticle($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $cate = new ArticleCateModel();
        $this->assign('cate',$cate->getAllCate());
        $this->assign('article',$article->getOneArticle($id));
        return $this->fetch();
    }



    /**
     * [del_article 删除文章]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_article()
    {
        $id = input('param.id');
        $cate = new ArticleModel();
        $flag = $cate->delArticle($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * [article_state 文章状态]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
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
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function index_cate(){

        $cate = new ArticleCateModel();
        $list = $cate->getAllCate();
        $this->assign('list',$list);
        return $this->fetch();
    }


    /**
     * [add_cate 添加分类]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function add_cate()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $cate = new ArticleCateModel();
            $flag = $cate->insertCate($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        return $this->fetch();
    }


    /**
     * [edit_cate 编辑分类]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function edit_cate()
    {
        $cate = new ArticleCateModel();

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
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_cate()
    {
        $id = input('param.id');
        $cate = new ArticleCateModel();
        $flag = $cate->delCate($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }


    /**
     * [cate_state 分类状态]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function cate_state()
    {
        $id=input('param.id');
        $status = Db::name('article_cate')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('article_cate')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('article_cate')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }  

}