<?php


namespace app\user\controller;

use app\user\model\UserBank;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class UserBankController extends AdminBaseController
{

    public function index(Request $request)
    {
        $where = [];
        $where[] = ['del_status', '=', 0];
        $data = input();
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $where[]=['u_id','=',$uid];
        }
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';

        if($start_time!=""){
            $where[]=['addtime','>=',strtotime($start_time)];
        }
        if($end_time!=""){
            $where[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        $status = isset($data['status']) ? $data['status']: '';
        if($status != '') $where[]=['status','=',$status];

        $list = UserBank::with(['bank'])->where($where)->order('id desc')->paginate(20);

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
            $rule = [
                'u_id'  => 'require',
                'name'  => 'require',
                'bank_id'  => 'require',
                'bank_card'  => 'require',
                'outlets'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'u_id.require' => '用户必须',
                'name.require' => '姓名必须',
                'bank_id.require' => '开户行必须',
                'bank_card.require' => '卡号必须',
                'outlets.require' => '开户网点必须',
                'status.require' => '是否默认必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['last_card'] = substr($data['bank_card'],-4);

            $res = UserBank::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }

        $users = Db::name('user')->where('user_type', 2)->field('id,user_login')->all()->toArray();
        $banks = Db::name('bank')->field('id,bank_name')->all()->toArray();
        $this->assign('users', $users);
        $this->assign('banks', $banks);
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'u_id'  => 'require',
                'name'  => 'require',
                'bank_id'  => 'require',
                'bank_card'  => 'require',
                'outlets'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'u_id.require' => '用户必须',
                'name.require' => '姓名必须',
                'bank_id.require' => '开户行必须',
                'bank_card.require' => '卡号必须',
                'outlets.require' => '开户网点必须',
                'status.require' => '是否默认必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['last_card'] = substr($data['bank_card'],-4);

            $res = UserBank::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = UserBank::with(['user'])->get($id);
        $banks = Db::name('bank')->field('id,bank_name')->all()->toArray();
        $users = Db::name('user')->where('user_type', 2)->field('id,user_login')->all()->toArray();
        $this->assign('info', $info);
        $this->assign('users', $users);
        $this->assign('banks', $banks);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = UserBank::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}