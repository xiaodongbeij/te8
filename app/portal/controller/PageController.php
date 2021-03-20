<?php

namespace app\portal\controller;
use cmf\controller\HomeBaseController;
use app\portal\service\PostService;

class PageController extends HomebaseController{
	public function index() {
        
        $postService = new PostService();
        $pageId      = $this->request->param('id', 0, 'intval');
        $page        = $postService->publishedPage($pageId);

        if (empty($page)) {
            abort(404, ' 页面不存在!');
        }

        $this->assign('page', $page);
		
		return $this->fetch();
	}
}