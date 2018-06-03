<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/27
 * Time: 11:37
 */

namespace app\admin\controller;


use app\admin\model\AuthenticationModel;
use think\Session;

class Authentication extends Base
{

    //待认证用户
    public function stay()
    {
        //查询待已提交用户
        $model = new AuthenticationModel();
        $members = $model->getAllAuthentication(1);
        $this->assign('members',$members);
        return $this->fetch();
    }

    //已认证用户
    public function already()
    {
        //查询待已审核用户
        $model = new AuthenticationModel();
        $members = $model->getAllAuthentication(2);
        $this->assign('members',$members);
        return $this->fetch();
    }
    //认证失败用户
    public function auth_error()
    {
        //查询待已审核用户
        $model = new AuthenticationModel();
        $members = $model->getAllAuthentication(0);
        $this->assign('members',$members);
        return $this->fetch();
    }

    //认证用户
    public function check()
    {
        $request = request();
        if ($request->isGet())
        {
            //查询用户信息
            $model = new AuthenticationModel();
            $member = $model->getOneAuthentication(input('id'));
            //判断是否有错误信息
            if (Session::has('message'))
            {
                $this->assign('message',Session::get('message'));
            }
            $this->assign('member',$member);
            return $this->fetch();
        }
    }

    //认证成功
    public function check_success()
    {
        $request = request();
        if ($request->isGet())
        {
            $model = new AuthenticationModel();
            $result = $model->checkSuccess(input('id'));
            if ($result)
            {
                return $this->success('认证成功！','stay');
            }else
            {
                return $this->error('认证失败，请重试！','stay');
            }

        }
    }

    //认证失败
    public function check_error()
    {
        $data = input();
        if (empty($data['error']))
        {
            return $this->redirect('check',['id'=>$data['id']],302,[
                'message'=>'* 失败信息必须填写'
            ]);
        }
        //插入失败信息
        $model = new AuthenticationModel();
        $result = $model->insertErrorMessage(input('id'),input('error'));
        if ($result)
        {
            return $this->success('提交成功！','stay');
        }else
        {
            return $this->error('提交失败，请重试!');
        }
    }

    //身份认证
    public function check_user()
    {
        $data = input();
        var_dump($data);
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apix/bankcard_check/bankcard_four?bankcardno='.$data['bankcardno'].'&name='.$data['name'].'&idcardno='.$data['idcardno'].'&phone='.$data['phone'];
        $header = array(
            'apikey: 您自己的apikey',
        );
        // 添加apikey到header
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        // 执行HTTP请求
        curl_setopt($ch,CURLOPT_URL,$url);
        $res = curl_exec($ch);
        $res = json_decode($res);
        if ($res['code'] == 0)
        {
            return $this->success('身份信息正确！','check?id='.$data['id']);
        }elseif ($res['code'] ==1)
        {
            return $this->error('身份信息不符！','check?id='.$data['id']);
        }elseif ($res['code' ==2])
        {
            return $this->error('未查到该身份信息！','check?id='.$data['id']);
        }
    }
}