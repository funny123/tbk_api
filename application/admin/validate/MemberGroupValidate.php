<?php

namespace app\admin\validate;
use think\Validate;

class MemberGroupValidate extends Validate
{
    protected $rule = [
        ['group_name', 'unique:member_group', '会员组已经存在']
    ];

}