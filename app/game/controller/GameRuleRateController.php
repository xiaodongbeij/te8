<?php


namespace app\game\controller;


use app\game\model\GameCaizhong;
use app\game\model\GameRuleRate;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class GameRuleRateController extends AdminBaseController
{
    public function index()
    {
        $where = [];
        $data = input();

        $cai_id = isset($data['cai_id']) ? $data['cai_id']: '';
        if($cai_id != '') $where[] = ['cai_id', '=', $cai_id];

        $list = GameRuleRate::with(['cz'])->where($where)->paginate(20);
        $cais = GameCaizhong::where('del_status', 0)->field('id,show_name')->select();

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('cais', $cais);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function add()
    {

    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'cai_id'  => 'require',
                'rate_name'  => 'require',
                'rate_code'  => 'require',
                'rate'  => 'require',
                'rule_name'  => 'require',
                'rule_code'  => 'require',
                'status'  => 'require',
            ];

            $msg = [
                'cai_id.require' => '彩种必须',
                'rate_name.require' => '下注名称',
                'rate_code.require' => '下注代码',
                'rate.require' => '赔率',
                'rule_name.require' => '玩法名称',
                'rule_code.require' => '玩法名称代码',
                'status.require' => '状态',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $res = GameRuleRate::update($data);
            if(!$res) $this->error("编辑失败");
            $this->success("编辑成功");
        }

        $id = input('id');
        $cais = Db::name('game_caizhong')->where('del_status', 0)->field('id,show_name')->all()->toArray();
        $info = GameRuleRate::get($id);
        $this->assign('cais', $cais);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {

    }
}