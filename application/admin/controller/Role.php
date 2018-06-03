<?php

namespace app\admin\controller;
use app\admin\model\Node;
use app\admin\model\UserType;
use think\Db;

class Role extends Base
{

    /**
     * [index 角色列表]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function index(){

        $key = input('key');
        $map = [];
        if($key&&$key!=="")
        {
            $map['title'] = ['like',"%" . $key . "%"];          
        }   
        $user = new UserType();    
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = $user->getAllRole($map);  //总数据
        $allpage = intval(ceil($count / $limits));       
        $lists = $user->getRoleByWhere($map, $Nowpage, $limits);  
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('val', $key);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }



    /**
     * [roleAdd 添加角色]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function roleAdd()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $role = new UserType();
            $flag = $role->insertRole($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        return $this->fetch();
    }



    /**
     * [roleEdit 编辑角色]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function roleEdit()
    {
        $role = new UserType();
        if(request()->isAjax()){
            $param = input('post.');
            $flag = $role->editRole($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign([
            'role' => $role->getOneRole($id)
        ]);
        return $this->fetch();
    }



    /**
     * [roleDel 删除角色]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function roleDel()
    {
        $id = input('param.id');
        $role = new UserType();
        $flag = $role->delRole($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * [role_state 用户状态]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function role_state()
    {
        $id = input('param.id');
        $status = Db::name('auth_group')->where('id',$id)->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('auth_group')->where('id',$id)->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('auth_group')->where('id',$id)->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }



    /**
     * [giveAccess 分配权限]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function giveAccess()
    {
        $param = input('param.');
        $node = new Node();
        //获取现在的权限
        if('get' == $param['type']){
            $nodeStr = $node->getNodeInfo($param['id']);
            return json(['code' => 1, 'data' => $nodeStr, 'msg' => 'success']);
        }
        //分配新权限
        if('give' == $param['type']){

            $doparam = [
                'id' => $param['id'],
                'rules' => $param['rule']
            ];
            $user = new UserType();
            $flag = $user->editAccess($doparam);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }
}