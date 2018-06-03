<?php

namespace app\admin\validate;

use think\Validate;

class AdPositionValidate extends Validate
{
    protected $rule = [
       'name|广告位名称'  => 'require',
       'orderby|排序'  	  => 'require',
    ];

}