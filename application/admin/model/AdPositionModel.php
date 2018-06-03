<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class AdPositionModel extends Model
{
    protected $name = 'ad_position';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * [getAll 根据条件获取全部数据]
     * @author [田建龙] [864491238@qq.com]
     */
    public function getAll($nowpage, $limits)
    {
        return $this->page($nowpage, $limits)->order('id asc')->select();       
    }

    /**
     * 插入信息
     * @param $param
     */
    public function insertAdPosition($param)
    {
        try{
            $result =  $this->validate('AdPositionValidate')->allowField(true)->save($param);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加广告位成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     * @param $param
     */
    public function editAdPosition($param)
    {
        try{
            $result =  $this->validate('AdPositionValidate')->allowField(true)->save($param, ['id' => $param['id']]);
            if(false === $result){
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '编辑广告位成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取一条信息
     * @param $id
     */
    public function getOne($id)
    {
        return $this->where('id', $id)->find();
    }


    /**
     * [getAll 获取全部广告位]
     * @author [田建龙] [864491238@qq.com]
     */
    public function getAllPosition()
    {
        return $this->order('id asc')->select();       
    }


    /**
     * 删除信息
     * @param $id
     */
    public function delAdPosition($id)
    {
        try{
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除广告位成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}