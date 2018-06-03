<?php
/**
 * Created by PhpStorm.
 * User: 15499
 * Date: 2018/3/27
 * Time: 17:02
 */

namespace app\admin\controller;


use app\admin\model\AgentModel;
use think\Session;

class Agent extends Base
{
    //代理系统首页
    public function agent()
    {
        $model = new AgentModel();
        $request = request();
        if ($request->isGet())
        {
            //查询代理赠币信息
            $agent = $model->getAgentCoinInfo();
            $this->assign('agent',$agent);
            return $this->fetch();
        }
    }

    //设置代理系统
    public function set_agent()
    {
        $request = request();
        if ($request->isGet())
        {
            //错误回显
            if (Session::has('message'))
            {
                $this->assign('message',Session::get('message'));
                $this->assign('data',Session::get('data'));
            }
            //展示代理设置页面
            return $this->fetch();
        }elseif ($request->isPost())
        {
            //处理设置数据
            $data = input();
            $validate = validate('AgentCoinValidate');
            $result = $validate->scene('agent_coin')->batch(true)->check($data);
            if (!$result)
            {
                $this->redirect('set_agent',[],302,[
                    'data' =>$data,
                    'message' => $validate->getError()
                ]);
            }

            //数据入库
            $model = new AgentModel();
            $data['create_time'] = time();
            $data['update_time'] = time();
            $result = $model->insertAgentCoin($data);
            if ($result)
            {
                return $this->success('代理模式设置成功！','agent');
            }else
            {
                return $this->error('代理模式设置失败，请重试','agent');
            }
        }
    }

    //修改代理系统
    public function edit_agent()
    {
        $request = request();
        if ($request->isGet())
        {

            //错误回显
            if (Session::has('message'))
            {
                $this->assign('message',Session::get('message'));
                $this->assign('data',Session::get('data'));
            }else
            {
                //查询代理模式
                //查询代理赠币信息
                $model = new AgentModel();
                $agent = $model->getAgentCoinInfo();
                $this->assign('data',$agent);

            }
            //展示代理设置页面
            return $this->fetch();
        }elseif ($request->isPost())
        {
            //处理设置数据
            $data = input();
            $validate = validate('AgentCoinValidate');
            $result = $validate->scene('agent_coin')->batch(true)->check($data);
            if (!$result)
            {
                $this->redirect('set_agent',[],302,[
                    'data' =>$data,
                    'message' => $validate->getError()
                ]);
            }

            //数据入库
            $model = new AgentModel();
            $data['update_time'] = time();
            $result = $model->editAgentCoin($data);
            if ($result)
            {
                return $this->success('代理模式设置成功！','agent');
            }else
            {
                return $this->error('代理模式设置失败，请重试','agent');
            }
        }
    }

}