<?php


namespace app\game\controller;


use app\game\model\GameCaizhong;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class GameCaizhongController extends AdminBaseController
{

    public function index()
    {
        $where = [];
        $data = input();

        $where[] = ['del_status', '=', 0];
        $cat_id = isset($data['cat_id']) ? $data['cat_id']: '';
        if($cat_id != '') $where[] = ['cat_id', '=', $cat_id];

        $show_name = isset($data['show_name']) ? $data['show_name']: '';
        if($show_name != '') $where[] = ['show_name', 'like', '%' . $show_name . '%'];

        $short_name = isset($data['short_name']) ? $data['short_name']: '';
        if($short_name != '') $where[] = ['short_name', 'like', '%'. $short_name . '%'];

        $type = isset($data['type']) ? $data['type']: '';
        if($type != '') $where[] = ['type', '=', $type];


        $list = GameCaizhong::with(['cate'])->where($where)->order('sort desc')->paginate(20);
        $cates = Db::name('game_cate')->where('del_status', 0)->field('id,name')->all()->toArray();
        $types = GameCaizhong::where('del_status', 0)->field('type,type_name')->group('type')->select();

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('cates', $cates);
        $this->assign('types', $types);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function add(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'cat_id'  => 'require',
                'show_name'  => 'require',
                'short_name'  => 'require',
                'type'  => 'require',
            ];

            $msg = [
                'cat_id.require' => '分类必须',
                'show_name.require' => '彩种名字',
                'short_name.require' => '彩种代码',
                'type.require' => '彩种类型',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $type = explode('-', $data['type']);
            $data['type'] = $type['0'];
            $data['type_name'] = $type['1'];
            $res = GameCaizhong::create($data);
            if(!$res) $this->error("添加失败");
            deleteCaiZhongKeys();
            $this->success("添加成功");
        }

        $cates = Db::name('game_cate')->where('del_status', 0)->field('id,name')->all()->toArray();
        $types = GameCaizhong::where('del_status', 0)->field('type,type_name')->group('type')->select();
        $this->assign('cates', $cates);
        $this->assign('types', $types);
        return $this->fetch();
    }

    public function edit(Request  $request)
    {
        if($request->isPost()){
            $rule = [
                'cat_id'  => 'require',
                'show_name'  => 'require',
                'short_name'  => 'require',
                'type'  => 'require',

            ];

            $msg = [
                'cat_id.require' => '分类必须',
                'show_name.require' => '彩种名字',
                'short_name.require' => '彩种代码',
                'type.require' => '彩种类型',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $type = explode('-', $data['type']);
            $data['type'] = $type['0'];
            $data['type_name'] = $type['1'];
            $res = GameCaizhong::update($data);
            if(!$res) $this->error("编辑失败");
            deleteCaiZhongKeys();
            $this->success("编辑成功");
        }

        $id = input('id');
        $cates = Db::name('game_cate')->where('del_status', 0)->field('id,name')->all()->toArray();
        $types = GameCaizhong::where('del_status', 0)->field('type,type_name')->group('type')->select();
        $info = GameCaizhong::get($id);
        $this->assign('cates', $cates);
        $this->assign('info', $info);
        $this->assign('types', $types);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $info = GameCaizhong::where('id', 'in', $ids)->find();
        $res = GameCaizhong::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("删除失败");
        
        deleteCaiZhongKeys();
        $this->success("删除成功");
    }
}