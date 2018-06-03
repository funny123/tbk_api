<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/27
 * Time: 17:59
 */

namespace app\admin\validate;


use think\Validate;

class AgentCoinValidate extends Validate
{
    protected $rule =[
        'level' => 'require|number'
    ];

    protected $field = [
        'level' => '等级'
    ];

    protected $scene = [
        'agent_coin' => ['level']
    ];

}