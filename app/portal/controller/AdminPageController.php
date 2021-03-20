<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\admin\model\ThemeModel;
use think\Db;

class AdminPageController extends AdminBaseController
{

    /**
     * 页面管理
     * @adminMenu(
     *     'name'   => '页面管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '页面管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('portal_admin_page_index_view');

        if (!empty($content)) {
            return $content;
        }

        $param = $this->request->param();

        $postService = new PostService();
        $data        = $postService->adminPageList($param);
        $data->appends($param);

        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('pages', $data->items());
        $this->assign('page', $data->render());

        return $this->fetch();
    }
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('portal_post');
        parent::listOrders($model);
		
		
		$action="更新页面管理排序 ";
        setAdminLog($action);
        
        $this->success("排序更新成功！");
    }	

    /**
     * 添加页面
     * @adminMenu(
     *     'name'   => '添加页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加页面',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $content = hook_one('portal_admin_page_add_view');

        if (!empty($content)) {
            return $content;
        }

        $themeModel     = new ThemeModel();
        $pageThemeFiles = $themeModel->getActionThemeFiles('portal/Page/index');
        $this->assign('page_theme_files', $pageThemeFiles);
        return $this->fetch();
    }

    /**
     * 添加页面提交
     * @adminMenu(
     *     'name'   => '添加页面提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加页面提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data['post'], 'AdminPage');
        if ($result !== true) {
            $this->error($result);
        }

        if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
            $data['post']['more']['photos'] = [];
            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
            }
        }

        if (!empty($data['file_names']) && !empty($data['file_urls'])) {
            $data['post']['more']['files'] = [];
            foreach ($data['file_urls'] as $key => $url) {
                $fileUrl = cmf_asset_relative_url($url);
                array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
            }
        }

        $portalPostModel = new PortalPostModel();
        $portalPostModel->adminAddPage($data['post']);
		
		
		$action="添加页面管理ID: ".$portalPostModel->id;
        setAdminLog($action);
		
		
        $this->success(lang('ADD_SUCCESS'), url('AdminPage/edit', ['id' => $portalPostModel->id]));
		
		

    }

    /**
     * 编辑页面
     * @adminMenu(
     *     'name'   => '编辑页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑页面',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $content = hook_one('portal_admin_page_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new PortalPostModel();
        $post            = $portalPostModel->where('id', $id)->find();

        $themeModel     = new ThemeModel();
        $pageThemeFiles = $themeModel->getActionThemeFiles('portal/Page/index');

        $routeModel         = new RouteModel();
        $alias              = $routeModel->getUrl('portal/Page/index', ['id' => $id]);
        $post['post_alias'] = $alias;
        $this->assign('page_theme_files', $pageThemeFiles);
        $this->assign('post', $post);

        return $this->fetch();
    }

    /**
     * 编辑页面提交
     * @adminMenu(
     *     'name'   => '编辑页面提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑页面提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data['post'], 'AdminPage');
        if ($result !== true) {
            $this->error($result);
        }

        if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
            $data['post']['more']['photos'] = [];
            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
            }
        }

        if (!empty($data['file_names']) && !empty($data['file_urls'])) {
            $data['post']['more']['files'] = [];
            foreach ($data['file_urls'] as $key => $url) {
                $fileUrl = cmf_asset_relative_url($url);
                array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
            }
        }

        $portalPostModel = new PortalPostModel();

        $portalPostModel->adminEditPage($data['post']);
		
		$action="编辑页面管理ID: ".$data['post']['id'];
        setAdminLog($action);

        $this->success(lang('SAVE_SUCCESS'));

    }

    /**
     * 删除页面
     * @author    iyting@foxmail.com
     * @adminMenu(
     *     'name'   => '删除页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除页面',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $portalPostModel = new PortalPostModel();
        $data            = $this->request->param();

		

        $result = $portalPostModel->adminDeletePage($data);
        if ($result) {
			
			
			$action="删除页面管理ID: ".$data['id'];
			setAdminLog($action);
			
            $this->success(lang('DELETE_SUCCESS'));
        } else {
            $this->error(lang('DELETE_FAILED'));
        }

    }

}
