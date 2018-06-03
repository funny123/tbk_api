<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class AuthenticationModel extends Model
{
    protected $name = 'member';  
    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取用户列表信息
     */
    public function getMemberByWhere($map, $Nowpage, $limits)
    {
        return $this->field('think_member.*,group_name')->join('think_member_group', 'think_member.group_id = think_member_group.id')
            ->where($map)->page($Nowpage, $limits)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     * @param $where
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }


    /**
     * 插入信息
     */
    public function insertMember($param)
    {
        try{
            $result = $this->validate('MemberValidate')->allowField(true)->save($param);
            if(false === $result){            
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     * @param $param
     */
    public function editMember($param)
    {
        try{
            $result =  $this->validate('MemberValidate')->allowField(true)->save($param, ['id' => $param['id']]);
            if(false === $result){            
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * 根据管理员id获取角色信息
     * @param $id
     */
    public function getOneMember($id)
    {
        return $this->where('id', $id)->find();
    }


    /**
     * 删除管理员
     * @param $id
     */
    public function delUser($id)
    {
        try{

            $this->where('id', $id)->delete();
            Db::name('auth_group_access')->where('uid', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];

        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    public function delMember($id)
    {
        try{
            $map['closed']=1;
            $this->save($map, ['id' => $id]);
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //查询所有待审核会员
    public function getAllAuthentication($map)
    {
        return Db::name('member')
            ->alias('m')
            ->join('person_info pi','m.person_info_id=pi.id')
            ->where('pi.status',$map)
            ->field('pi.*,m.account member_account,m.mobile member_mobile,m.id member_id')
            ->select();
    }

    //查询认证用户信息
    public function getOneAuthentication($id)
    {
        return Db::name('member')
            ->alias('m')
            ->join('person_info pi','m.person_info_id=pi.id','LEFT')
            ->where('m.id',$id)
            ->field('pi.*,m.id member_id,m.account member_account,m.mobile member_mobile')
            ->find();
    }

    //认证成功
    public function checkSuccess($id)
    {
        return Db::name('member')
            ->alias('m')
            ->where('m.id',$id)
            ->join('person_info pi','m.person_info_id=pi.id','LEFT')
            ->update([
                'pi.status'=>2,
                'pi.update_time'=>time()
            ]);
    }

    //插入失败信息
    public function insertErrorMessage($id,$errorData)
    {
        $person_info_id = Db::name('member')->where('id',$id)->find()['person_info_id'];
        return Db::name('person_info')
            ->where('id',$person_info_id)
            ->update([
                'error_message' => $errorData,
                'status'        => 0,
                'update_time'   => time()
            ]);
    }

}