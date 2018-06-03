<?php

namespace app\admin\controller;
use app\admin\model\LogModel;
use think\Db;
use com\IpLocation;
 
class Log extends Base
{

    /**
     * [operate_log 操作日志]
     */
    public function operate_log()
    {

        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['admin_id'] =  $key;          
        }      
        $arr=Db::name("admin")->column("id,username"); //获取用户列表      
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = Db::name('log')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = Db::name('log')->where($map)->page($Nowpage, $limits)->order('add_time desc')->select();       
        $Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
        foreach($lists as $k=>$v){
            $lists[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $lists[$k]['ipaddr'] = $Ip->getlocation($lists[$k]['ip']);
        }  
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('count', $count);
        $this->assign("search_user",$arr);
        $this->assign('val', $key);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }


    /**
     * [del_log 删除日志]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_log()
    {
        $id = input('param.id');
        $log = new LogModel();
        $flag = $log->delLog($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //错误日志
    public function error_log()
    {
        $key = input('key');
        $map = [];
        if($key&&$key!==""){

        }
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = Db::name('error_log')->where($map)->count();//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = Db::name('error_log')->where($map)->page($Nowpage, $limits)->order('create_time desc')->select();
        foreach ($lists as $k=>&$v)
        {
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count);
        $this->assign('val', $key);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();

    }
}