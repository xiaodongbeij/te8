<?php


namespace app\admin\controller;


use app\admin\model\Channel;
use cmf\controller\AdminBaseController;
use think\Request;
use think\Db;
use think\Validate;

class ChannelController extends AdminbaseController
{
    
    public $msg = [
                'shop_id.require' => '商户id必须',
                'channel_name.require' => '通道名称必须',
                'status.require' => '通道状态必须',
                'action.require' => '请求地址必须',
                'key.require' => '商户秘钥必须',
                'notify_url.require' => '回调地址必须',
                'return_url.require' => '跳转地址必须',
                'start_time.require' => '开启时间',
                'end_time.require' => '关闭时间',
                'bank_name.require' => '银行名字',
                'bank_no.require' => '银行卡号',
                'name.require' => '姓名',
            ];
            
    public function index()
    {

        $where = [];
        $data = input();
        $where[] = ['del_status', '=', 0];

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['addtime', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['addtime', '>=' ,strtotime($end_time)];

        $status = isset($data['status']) ? $data['status']: '';
        if($status != '') $where[]=['status', '=', $status];

        $pay_type = isset($data['pay_type']) ? $data['pay_type']: '';
        if($pay_type != '') $where[]=['pay_type', '=', $pay_type];

        $shop_id = isset($data['shop_id']) ? $data['shop_id']: '';
        if($shop_id != '') $where[]=['shop_id', '=', $shop_id];

        $list = Channel::where($where)->order('id desc')->paginate(20);

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function add(Request $request)
    {
        if($request->isPost()){
            
            $pay_type = input('pay_type');
            $rule = [
                'channel_name'  => 'require',
                'status'  => 'require',
                'start_time'  => 'require',
                'end_time'  => 'require',
            ];
            
            if($pay_type == 3)
            {
                $rule['bank_name'] = 'require';
                $rule['bank_no'] = 'require';
                $rule['name'] = 'require';
            }else{
                $rule['key'] = 'require';
                $rule['notify_url'] = 'require';
                $rule['return_url'] = 'require';
                $rule['action'] = 'require';
                $rule['shop_id'] = 'require';
            }

            
            $data = input();
            $validate = new Validate($rule, $this->msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = Channel::create($data);

            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }
        $list = Db::name('bank')->all();
        $pay_type = Db::name('paytype')->all();
        $this->assign('list', $list);
        $pay_type = Db::name('paytype')->all();
        $this->assign('pay_type', $pay_type);
        return $this->fetch();
    }

    public function edit(Request  $request)
    {
        if($request->isPost()){
            $pay_type = input('pay_type');
            $rule = [
                'channel_name'  => 'require',
                'status'  => 'require',
                'start_time'  => 'require',
                'end_time'  => 'require',
            ];
            
            if($pay_type == 3)
            {
                $rule['bank_name'] = 'require';
                $rule['bank_no'] = 'require';
                $rule['name'] = 'require';
            }else{
                $rule['key'] = 'require';
                $rule['notify_url'] = 'require';
                $rule['return_url'] = 'require';
                $rule['action'] = 'require';
                $rule['shop_id'] = 'require';
            }
            $data = input();
            $validate = new Validate($rule, $this->msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = Channel::update($data);

            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = Channel::get($id);
        $this->assign('info', $info);
        $list = Db::name('bank')->all();
        $pay_type = Db::name('paytype')->all();
        $this->assign('pay_type', $pay_type);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function del(Request $request)
    {
        $ids = input();

        $res = Channel::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}