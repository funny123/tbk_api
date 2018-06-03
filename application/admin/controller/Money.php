<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/22
 * Time: 13:29
 */

namespace app\admin\controller;


use app\admin\model\MoneyModel;
use think\Controller;
use think\Db;

class Money extends Base
{
    //币值调整
    public function adjustment()
    {
        $request = request();
        if ($request->isGet())
        {
            //展示当前币价
            $money = new MoneyModel();
            $moneyData = $money->getOneMoney();
            $this->assign('moneyData',$moneyData);
            return $this->fetch();
        }
    }

    //减少币值
    public function reduce()
    {
        $request = request();
        if ($request->isPost())
        {
            if (input('reduce') == 0)
            {
                return $this->error('未修改币值');
            }
            //数据验证

            //数据入库
            $price  = Db::name('money')->where('id',1)->find()['price'];//原始数据
            //新增币值历史记录
            $historyData['history_price'] = $price;
            $historyData['history_date']  = time();
            $historyData['create_time']   = time();
            $historyData['update_time']   = time();
            $historyResult = Db::name('money_history')->insert($historyData);
            if (!$historyResult)
            {
                return $this->error('币值下降失败！');
            }
            $data['price']  = $price-input('reduce');
            $data['update_time'] = time();
            $result = Db::name('money')->where('id',1)->update($data);
            if ($result)
            {
                return $this->success('币值下降成功');
            }else
            {
                return $this->error('币值下降失败！');
            }
        }
    }

    //增加币值
    public function add()
    {
        $request = request();
        if ($request->isPost())
        {
            if (input('add') == 0)
            {
                return $this->error('未输入增加币值');
            }
            $res = Db::name('day_price')->count('time');
            if ($res>=1)
            {
                return $this->error('未到币值更新时间！','adjustment');
            }
            //数据验证
            $start      = Db::name('money')->field('price')->find()['price'];    //当前币值
            $add        = input('add');                                                 //增加币值
            $add_range  = input('add_range');                                           //变化幅度
            $flag       = [1,2];                                                            //1加，2减
            $arr        = array();
            $data       = array();
            for ($i = 0;$i<24;$i++)
            {
                $j = $i-1;
                if ($j<0)
                {
                    $arr[$i] = $start;
                    $data[$i]['time']  = $i;
                    $data[$i]['price'] = $arr[$i];
                    continue;
                }
                if (array_rand($flag) == 1 )
                {
                    $arr[$i] = $arr[$j]+$add_range;

                }else
                {
                    $arr[$i] = $arr[$j]-$add_range;
                }
                $data[$i]['time']  = $i;
                $data[$i]['price'] = $arr[$i];
            }
            $data[23]['price'] = $start+$add;
            //数据入库
            $result = Db::name('day_price')->insertAll($data);
            if ($result)
            {
                return $this->success('币值增加成功！');
            }else
            {
                return $this->error('币值增加失败');
            }
        }
    }

    //创建货币
    public function money()
    {
        $key = input('key');
        $map = '';
        $member = new MoneyModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = $member->getMoneyByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();
    }

    //编辑货币
    public function edit_money()
    {
        $money = new MoneyModel();
        if (request()->isGet())
        {
            $moneyData = $money->getOneMoney(input('id'));
            $this->assign('moneyData',$moneyData);
        }else if (request()->isPost())
        {
            $data = input();
            $result = Db::name('money')->where('id',$data['id'])->update($data);
            if ($result !== false)
            {
                return $this->success('货币修改成功！','money');
            }else
            {
                return $this->error('信息修改失败！','money');
            }
        }
        
        return $this->fetch();
    }

    //历史币价
    public function history()
    {
        $key = input('key');
        $map='';
        if($key&&$key!=="")
        {
            $map['account|nickname|mobile'] = ['like',"%" . $key . "%"];
        }
        $member = new MoneyModel();
        $Nowpage = input('get.page') ? input('get.page'):1;
        $limits = config('list_rows');// 获取总条数
        $count = $member->getAllHistoryCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = $member->getAllHistory($map, $Nowpage, $limits);
        foreach ($lists as &$v)
        {
            $v['history_date'] = date('Y-m-d H:i:s',$v['history_date']);
        }
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('val', $key);
        if(input('get.page'))
        {
            return json($lists);
        }
        return $this->fetch();

    }

}