<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/27
 * Time: 17:30
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class AgentModel extends Model
{

    //查询代理模式
    public function getAgentCoinInfo()
    {
        return Db::name('agent_coin')
            ->find();
    }

    //设置代理模式
    public function insertAgentCoin($data)
    {
        return Db::name('agent_coin')
            ->insert($data);
    }

    //修改代理模式
    public function editAgentCoin($data)
    {
        return Db::name('agent_coin')
            ->where('id',1)
            ->update($data);
    }
}