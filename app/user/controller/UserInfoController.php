<?php


namespace app\user\controller;


use app\user\model\UserInfo;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class UserInfoController extends AdminBaseController
{
    public function index()
    {
        $where = [];
        $where[] = ['del_status', '=', 0];
        $data = input();
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $where[]=['user_id','=',$uid];
        }

        $list = UserInfo::where($where)->order('id desc')->paginate(20);

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
                'user_id'  => 'require',
                'wxpay_img'  => 'require',
                'wxpay_account'  => 'require',
            ];

            $msg = [
                'user_id.require' => '用户必须',
                'wxpay_img.require' => '微信收款二维码必须',
                'wxpay_account.require' => '微信收款账户行必须',
            ];
            $data = input();
//            dump($data);die;
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['money_passwd'] = isset($data['money_passwd']) ? $data['money_passwd'] : null;
            if($data['money_passwd'] != null) $data['money_passwd'] = password_hash($data['money_passwd'],PASSWORD_DEFAULT);

            if($info = UserInfo::where('user_id', $data['user_id'])->find()) $this->error('此用户已存在');
            $res = UserInfo::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }

        $users = Db::name('user')->where('user_type', 2)->field('id,user_login')->all()->toArray();
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'wxpay_img'  => 'require',
                'wxpay_account'  => 'require',
            ];

            $msg = [
                'wxpay_img.require' => '微信收款二维码必须',
                'wxpay_account.require' => '微信收款账户行必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['money_passwd'] = isset($data['money_passwd']) ? $data['money_passwd'] : null;
            if($data['money_passwd'] != null) {
                $data['money_passwd'] = password_hash($data['money_passwd'],PASSWORD_DEFAULT);
            }else{
                unset($data['money_passwd']);
            }

            $res = UserInfo::update($data);
            if(!$res) $this->error("更新失败");
            $this->success("ok");
        }

        $id = input('id');
        $users = Db::name('user')->where('user_type', 2)->field('id,user_login')->all()->toArray();
        $info = UserInfo::with(['user'])->get($id);

        $this->assign('users', $users);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = UserInfo::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("删除失败");
        $this->success("ok");
    }
}