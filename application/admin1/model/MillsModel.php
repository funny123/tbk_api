<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class MillsModel extends Model
{
    protected $name = 'mills';
    
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;


    /**
     * 根据搜索条件获取矿机列表信息
     */
    public function getMillsByWhere($map, $Nowpage, $limits)
    {
        return $this->field('think_mills.*,think_mills_cate.name cate_name')->join('think_mills_cate', 'think_mills.cate_id = think_mills_cate.id')->where($map)->page($Nowpage, $limits)->order('id desc')->select();
    }
    
    
    /**
     * [insertArticle 添加矿机]
*/
    public function insertMills($param)
    {
        try{
            $result = $this->allowField(true)->save($param);
            if(false === $result){             
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '矿机添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    /**
     * [updateArticle 编辑矿机]
     */
    public function updateMills($param)
    {
        try{
            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
            if(false === $result){          
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '矿机修改成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    /**
     * [getOneArticle 根据文章id获取一条信息]
     * @author [田建龙] [864491238@qq.com]
     */
    public function getOneMills($id)
    {
        return $this->where('id', $id)->find();
    }



    /**
     * [delArticle 删除文章]
     * @author [田建龙] [864491238@qq.com]
     */
    public function delMills($id)
    {
        try{
            $this->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '矿机删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}