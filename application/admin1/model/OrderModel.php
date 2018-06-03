<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class OrderModel extends Model
{
    protected $name = 'order';
    
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;


    /**
     * [getAllOrder 获取全部订单]
     */
    public function getAllOrder($map)
    {
        return Db::name('order')
            ->alias('o')
            ->join('member m','o.buy_id=m.id','LEFT')
            ->join('member sm','o.sell_id=sm.id','LEFT')
            ->field('o.*,m.mobile buy_mobile,m.account buy_account,sm.mobile sell_mobile,sm.account sell_account')
            ->where($map)
            ->order('o.create_time desc')
            ->select();
    }



    /**
     * [getOneMenu 根据分类id获取一条信息]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function getOneCate($id)
    {
        return $this->where('id', $id)->find();
    }



    /**
     * [delMenu 删除分类]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function delOrder($id)
    {
        try{
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '分类删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}