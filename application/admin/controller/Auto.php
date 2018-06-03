<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/23
 * Time: 13:30
 */

namespace app\admin\controller;


class Auto
{

    public function index()
    {
        $start  = 3.002;
        $end    = 3.005;
        echo '起始价格：'.$start.'<br>';
        echo '结束价格：'.$end.'<br>';
        $num = $end-$start;
        $ave_num = floatval('0.001');
        $flag = ['add','reduce'];
        $arr = [];
        for ($i=0;$i<24;$i++)
        {
            $j = 0;
            if ($i>0)
            {
                $j = $i-1;
            }else
            {
                $arr[0] = $start;
            }
            $now_flag = array_rand($flag);
            if ($now_flag == '+')
            {
                $arr[$i] = $arr[$j]+$ave_num;
            }else
            {
                $arr[$i] = $arr[$j]-$ave_num;
            }
        }
        $arr[23] = $end;
        echo '<pre>';
        var_dump($arr);
    }
}