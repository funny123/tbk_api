<?php

namespace app\admin\controller;
use app\admin\model\MemberModel;
use app\admin\model\MemberGroupModel;
use think\Db;

class Member extends Base
{
    //*********************************************会员组*********************************************//
    /**
     * [group 会员组]
     * @author [田建龙] [864491238@qq.com]
     */
    public function group(){

        $key = input('key');
        $map = [];
        if($key&&$key!==""){
            $map['group_name'] = ['like',"%" . $key . "%"];          
        }      
        $group = new MemberGroupModel(); 
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');
        $count = $group->getAllCount($map);         //获取总条数
        $allpage = intval(ceil($count / $limits));  //计算总页面      
        $lists = $group->getAll($map, $Nowpage, $limits);  
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('val', $key);
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }

    /**
     * [add_group 添加会员组]
     * @author [田建龙] [864491238@qq.com]
     */
    public function add_group()
    {
        if(request()->isAjax()){
            $param = input('post.');
            $group = new MemberGroupModel();
            $flag = $group->insertGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }


    /**
     * [edit_group 编辑会员组]
     * @author [田建龙] [864491238@qq.com]
     */
    public function edit_group()
    {
        $group = new MemberGroupModel();
        if(request()->isPost()){           
            $param = input('post.');
            $flag = $group->editGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('group',$group->getOne($id));
        return $this->fetch();
    }


    /**
     * [del_group 删除会员组]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_group()
    {
        $id = input('param.id');
        $group = new MemberGroupModel();
        $flag = $group->delGroup($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [group_status 会员组状态]
     * @author [田建龙] [864491238@qq.com]
     */
    public function group_status()
    {
        $id=input('param.id');
        $status = Db::name('member_group')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('member_group')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('member_group')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }   
    } 


    //*********************************************会员列表*********************************************//
    /**
     * 会员列表
     * @author [田建龙] [864491238@qq.com]
     */
    public function index(){

        $key = input('key');
        $map['closed'] = 0;//0未删除，1已删除
        if($key&&$key!=="")
        {
            $map['account|nickname|mobile'] = ['like',"%" . $key . "%"];          
        }
        $member = new MemberModel();       
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));       
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);   
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
     * 添加会员
     * @author [田建龙] [864491238@qq.com]
     */
    public function add_member()
    {
        if(request()->isAjax()){

            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $group = new MemberGroupModel();
        $this->assign('group',$group->getGroup());
        return $this->fetch();
    }


    /**
     * 编辑会员
     * @author [田建龙] [864491238@qq.com]
     */
    public function edit_member()
    {
        $member = new MemberModel();
        if(request()->isAjax()){
            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            $flag = $member->editMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $group = new MemberGroupModel();
        $this->assign([
            'member' => $member->getOneMember($id),
            'group' => $group->getGroup()
        ]);
        return $this->fetch();
    }


    /**
     * 删除会员
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_member()
    {
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * 会员状态
     * @author [田建龙] [864491238@qq.com]
     */
    public function member_status()
    {
        $id = input('param.id');
        $status = Db::name('member')->where('id',$id)->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('member')->where('id',$id)->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }

    //实名认证审核
    public function authentication()
    {
        //查询所有待审核会员
        $model = new MemberModel();
        $members = $model->getAllAuthentication();
        $this->assign('members',$members);
        return $this->fetch();

    }

}