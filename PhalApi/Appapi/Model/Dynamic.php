<?php
if (!session_id()) session_start();
class Model_Dynamic extends PhalApi_Model_NotORM {
	/* 发布视频 */
	public function setDynamic($data) {
		$uid=$data['uid'];
        $thumb=$data['thumb'];
        $video_thumb=$data['video_thumb'];
        $href=$data['href'];
        //审核开关
		$configpri=getConfigPri();
		$dynamic_auth=$configpri['dynamic_auth'];
		if($dynamic_auth=='1'){//动态发布认证关闭
			$isauth=isAuth($uid);
			if(!$isauth){
				return 1003;
			}
		}
        
        $nowtime=time();
		$status='0';
		
		//获取后台配置的初始曝光值
		
		$dynamic_switch=$configpri['dynamic_switch'];
		if($dynamic_switch=='0'){//动态发布审核关闭：动态状态默认：通过
			$status='1';//
		}
        $data['status']=$status;
        $data['addtime']=$nowtime;
        $data['uptime']=$nowtime;

		$result=DI()->notorm->dynamic->insert($data);
        if(!$result){
			return 1004;
        }
		
		//更新话题使用次数
		if($data['labelid']!=0){
			DI()->notorm->dynamic_label
				->where("id = '{$data['labelid']}'")
				->update( array('use_nums' => new NotORM_Literal("use_nums + 1") ) );
			
			//更新话题缓存
			$key='LabelInfo_'.$data['labelid'];
			delcache($key);
		}
		return $result;
	}	

	/* 评论/回复 */
    public function setComment($data) {
    	$dynamicid=$data['dynamicid'];
		/* 更新 动态 */
		DI()->notorm->dynamic
            ->where("id = '{$dynamicid}'")
		 	->update( array('comments' => new NotORM_Literal("comments + 1") ) );
	
        $res=DI()->notorm->dynamic_comments
            ->insert($data);
			
		$videoinfo=DI()->notorm->dynamic
					->select("comments")
					->where('id=?',$dynamicid)
					->fetchOne();
		$count=DI()->notorm->dynamic_comments
					->where("commentid='{$data['commentid']}'")
					->count();
		$rs=array(
			'comments'=>$videoinfo['comments'],
			'replys'=>$count,
		);

		return $rs;	
    }			

	
	/* 点赞 */
	public function addLike($uid,$dynamicid){
		$rs=array(
			'islike'=>'0',
			'likes'=>'0',
		);
  
		$dyinfo = $this->getDynamic($uid,$dynamicid);
        if(!$dyinfo){
			return 1001;
        }
		if($dyinfo['uid']==$uid){
			return 1002;//不能给自己点赞
		}
		
        $islike = isdynamiclike($uid,$dynamicid);
        
        if($islike){
            /* 已点赞 - 取消 */
            $this->reduceLike($uid,$dynamicid);
            $nums=$dyinfo['likes']-1;
        }else{
            /* 未点赞 - 添加 */
            $this->addtoLike($uid,$dynamicid);
            $nums=$dyinfo['likes']+1;
        }
        
        $rs['islike']=$islike?'0':'1';
        
        $rs['likes']=NumberFormat($nums);
		
		return $rs; 		
	}
	
	
    
    /* 点赞+ */
	public function addtoLike($uid,$dynamicid) {
		$rs=DI()->notorm->dynamic
				->where("id = ?",$dynamicid)
				->update( array('likes' => new NotORM_Literal("likes + 1") ) );
                
        DI()->notorm->dynamic_like
                    ->insert(array("uid"=>$uid,"dynamicid"=>$dynamicid,"addtime"=>time() ));
		return $rs;
	}
    
    /* 点赞- */
	public function reduceLike($uid,$dynamicid) {
		$rs=DI()->notorm->dynamic
                    ->where("id = '{$dynamicid}' and likes>0")
                    ->update( array('likes' => new NotORM_Literal("likes - 1") ) );
        
        DI()->notorm->dynamic_like
                    ->where("uid='{$uid}' and dynamicid='{$dynamicid}'")
                    ->delete();
                            
		return $rs;
	}
	


	/* 评论/回复 点赞 */
	public function addCommentLike($uid,$commentid){
		$rs=array(
			'islike'=>'0',
			'likes'=>'0',
		);

		//根据commentid获取对应的评论信息
		$commentinfo=DI()->notorm->dynamic_comments
			->where("id='{$commentid}'")
			->fetchOne();

		if(!$commentinfo){
			return 1001;
		}
		
		$islike = $this->isCommentlike($uid,$commentid);
        
        if($islike){
            /* 已点赞 - 取消 */
            $this->reduceCommentLike($uid,$commentid);
            $nums=$commentinfo['likes']-1;
        }else{
            /* 未点赞 - 添加 */
            $this->addtoCommentLike($uid,$commentid,$commentinfo['uid'],$commentinfo['dynamicid']);
            $nums=$commentinfo['likes']+1;
        }
		  
        $rs['islike']=$islike?'0':'1';
		$rs['likes']=NumberFormat($nums);

		return $rs; 		
	}
	
	 /* 评论是否点赞 */
	public function isCommentlike($uid,$commentid) {
        
		$like=DI()->notorm->dynamic_comments_like
			->select("id")
			->where("uid='{$uid}' and commentid='{$commentid}'")
			->fetchOne();
        if($like){
            return '1';
        }
        
		return '0';
	}
	/* 取消评论点赞- */
	public function reduceCommentLike($uid,$commentid) {
		DI()->notorm->dynamic_comments_like
						->where("uid='{$uid}' and commentid='{$commentid}'")
						->delete();
			
		$rs=DI()->notorm->dynamic_comments
			->where("id = '{$commentid}' and likes>0")
			->update( array('likes' => new NotORM_Literal("likes - 1") ) );
		 
		return $rs;
	}
	/* 评论点赞- */
	public function addtoCommentLike($uid,$commentid,$touid,$dynamicid) {
		DI()->notorm->dynamic_comments_like
						->insert(array("uid"=>$uid,"commentid"=>$commentid,"addtime"=>time(),"touid"=>$touid,"dynamicid"=>$dynamicid));
			
		$rs=DI()->notorm->dynamic_comments
			->where("id = '{$commentid}'")
			->update( array('likes' => new NotORM_Literal("likes + 1") ) );
		return $rs;
	}
	

	/* 关注用户 动态列表*/
	public function getAttentionDynamic($uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
	
		$dynamic=array();
		$attention=DI()->notorm->user_attention
				->select("touid")
				->where("uid='{$uid}'")
				->fetchAll();
		
		if($attention){
			
			$uids=array_column($attention,'touid');
			$touids=implode(",",$uids);
			
		
			$where="uid in ({$touids}) and  isdel=0 and status=1";
			
			$dynamic=DI()->notorm->dynamic
					->select("*")
					->where($where)
					->order("addtime desc")
					->limit($start,$nums)
					->fetchAll();
			if(!$dynamic){
				return array();
			}
			
			foreach($dynamic as $k=>$v){
				$v=handleDynamic($uid,$v);
            
                $dynamic[$k]=$v;
				
			}
		}
	
		return $dynamic;		
	} 		
	/*最新 动态列表*/
	public function getNewDynamic($uid,$lng,$lat,$p) {
			 if($p<1){
				$p=1;
			}
			$nums=20;
			$start=($p-1)*$nums;
			
			$where=" isdel=0 and status=1 ";
			$dynamic=array();
			if($p!=1){
				$endtime=$_SESSION['new_dstarttime'];
				if($endtime){
					$where.=" and addtime < {$endtime}";
				}
			}	
			$dynamic=DI()->notorm->dynamic
					->select("*")
					->where($where)
					->order("addtime desc")
					->limit(0,$nums)
					->fetchAll();
			if(!$dynamic){
				return array();
			}		
			foreach($dynamic as $k=>$v){
				$v=handleDynamic($uid,$v);
                $dynamic[$k]=$v;
			}		
		
			if($dynamic){
				$last=end($dynamic);
				$_SESSION['new_dstarttime']=$last['addtime'];
			}
			return $dynamic;		
	} 		
	
	
	/* 个人主页动态 */
	public function getHomeDynamic($uid,$touid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
		
		
		if($uid==$touid){  //自己的视频（需要返回视频的状态前台显示）
			$where=" uid={$uid} and isdel='0' and status=1";
		}else{  //访问其他人的主页视频
			$where="uid={$touid} and isdel='0' and status=1";
		}
		
		$dynamic=DI()->notorm->dynamic
				->select("*")
				->where($where)
				->order("addtime desc")
				->limit($start,$nums)
				->fetchAll();
		if(!$dynamic){
				return array();
		}
		foreach($dynamic as $k=>$v){
				$v=handleDynamic($uid,$v);
                $dynamic[$k]=$v;
		}		
		
		return $dynamic;
		
	}
	//推荐动态列表
	public function getRecommendDynamics($uid,$p){
        if($p<1){
            $p=1;
        }
		$pnums=20;
		$start=($p-1)*$pnums;
		
		$configPri=getConfigPri();
		//获取私密配置里的评论权重和点赞权重
		$comment_weight=$configPri['comment_weight'];
		$like_weight=$configPri['like_weight'];
	
		$prefix= DI()->config->get('dbs.tables.__default__.prefix');

		//热度值 = 点赞数*点赞权重+评论数*评论权重
		//排序规则：（曝光值+热度值）
		//曝光值从视频发布开始，每小时递减1，直到0为止

		$info=DI()->notorm->dynamic
		// ->select("*,(ceil(comments * ".$comment_weight." + likes * ".$like_weight.") + show_val) as recomend")
		->select("*,ceil(comments * ".$comment_weight." + likes * ".$like_weight.") as recomend")
		->where("isdel=0 and status=1 ")
		// ->where('not id',$where)
		->order("recommend_val desc,recomend desc,addtime desc")
		->limit($start,$pnums)
		->fetchAll();
		if(!$info){
			return 1001;
		}
		foreach ($info as $k => $v) {
			$v=handleDynamic($uid,$v);
            $info[$k]=$v;
		}
		return $info;
	}
	
	
	/* 评论列表 */
	public function getComments($uid,$dynamicid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
		$comments=DI()->notorm->dynamic_comments
					->select("*")
					->where("dynamicid='{$dynamicid}' and parentid='0'")
					->order("addtime desc")
					->limit($start,$nums)
					->fetchAll();
		foreach($comments as $k=>$v){
			$comments[$k]['userinfo']=getUserInfo($v['uid']);				
			$comments[$k]['datetime']=datetime($v['addtime']);	
			$comments[$k]['likes']=NumberFormat($v['likes']);	
			if($uid){
				$comments[$k]['islike']=(string)$this->ifCommentLike($uid,$v['id']);	
			}else{
				$comments[$k]['islike']='0';	
			}
			
			if($v['touid']>0){
				$touserinfo=getUserInfo($v['touid']);
			}
			if(!$touserinfo){
				$touserinfo=(object)array();
				$comments[$k]['touid']='0';
			}
			$comments[$k]['touserinfo']=$touserinfo;

			$count=DI()->notorm->dynamic_comments
					->where("commentid='{$v['id']}'")
					->count();
			$comments[$k]['replys']=$count;
            
            /* 回复 */
            $reply=DI()->notorm->dynamic_comments
					->select("*")
					->where("commentid='{$v['id']}'")
					->order("addtime desc")
					->limit(0,1)
					->fetchAll();
            foreach($reply as $k1=>$v1){
                
                $v1['userinfo']=getUserInfo($v1['uid']);				
                $v1['datetime']=datetime($v1['addtime']);	
                $v1['likes']=NumberFormat($v1['likes']);	
                $v1['islike']=(string)$this->ifCommentLike($uid,$v1['id']);
                if($v1['touid']>0){
                    $touserinfo=getUserInfo($v1['touid']);
                }
                if(!$touserinfo){
                    $touserinfo=(object)array();
                    $v1['touid']='0';
                }
                
                if($v1['parentid']>0 && $v1['parentid']!=$v['id']){
                    $tocommentinfo=DI()->notorm->dynamic_comments
                        ->select("content")
                        ->where("id='{$v1['parentid']}'")
                        ->fetchOne();
                }else{
                    $tocommentinfo=(object)array();
                    $touserinfo=(object)array();
                    $v1['touid']='0';
                }
                $v1['touserinfo']=$touserinfo;
                $v1['tocommentinfo']=$tocommentinfo;


                $reply[$k1]=$v1;
            }
            
            $comments[$k]['replylist']=$reply;
		}
		
		$commentnum=DI()->notorm->dynamic_comments
					->where("dynamicid='{$dynamicid}'")
					->count();
		
		$rs=array(
			"comments"=>$commentnum,
			"commentlist"=>$comments,
		);
		
		return $rs;
	}

	/* 回复列表 */
	public function getReplys($uid,$commentid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
		$comments=DI()->notorm->dynamic_comments
					->select("*")
					->where("commentid='{$commentid}'")
					->order("addtime desc")
					->limit($start,$nums)
					->fetchAll();


		foreach($comments as $k=>$v){
			$comments[$k]['userinfo']=getUserInfo($v['uid']);				
			$comments[$k]['datetime']=datetime($v['addtime']);	
			$comments[$k]['likes']=NumberFormat($v['likes']);	
			$comments[$k]['islike']=(string)$this->ifCommentLike($uid,$v['id']);
			if($v['touid']>0){
				$touserinfo=getUserInfo($v['touid']);
			}
			if(!$touserinfo){
				$touserinfo=(object)array();
				$comments[$k]['touid']='0';
			}
			


			if($v['parentid']>0 && $v['parentid']!=$commentid){
				$tocommentinfo=DI()->notorm->dynamic_comments
					->select("content")
					->where("id='{$v['parentid']}'")
					->fetchOne();
			}else{

				$tocommentinfo=(object)array();
				$touserinfo=(object)array();
				$comments[$k]['touid']='0';

			}
			$comments[$k]['touserinfo']=$touserinfo;
			$comments[$k]['tocommentinfo']=$tocommentinfo;
		}
		
		return $comments;
	}
	
	
	
	/* 评论/回复 是否点赞 */
	public function ifCommentLike($uid,$commentid){
		$like=DI()->notorm->dynamic_comments_like
				->select("id")
				->where("uid='{$uid}' and commentid='{$commentid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}
	
	
	/* 删除动态 */
	public function del($uid,$dynamicid){
		
		$result=DI()->notorm->dynamic
					->select("*")
					->where("id='{$dynamicid}' and uid='{$uid}'")
					->fetchOne();	
		if($result){
			// 删除 评论记录
			 /*DI()->notorm->dynamic_comments
						->where("dynamicid='{$dynamicid}'")
						->delete(); 
			//删除动态评论喜欢
			DI()->notorm->dynamic_comments_like
						->where("dynamicid='{$dynamicid}'")
						->delete(); 
			
			// 删除  点赞
			 DI()->notorm->dynamic_like
						->where("dynamicid='{$dynamicid}'")
						->delete(); 
			//删除动态举报
			DI()->notorm->dynamic_report
						->where("dynamicid='{$dynamicid}'")
						->delete(); 
			// 删除动态 
			 DI()->notorm->dynamic
						->where("id='{$dynamicid}'")
						->delete();	*/ 

			//将喜欢的动态列表状态修改
			DI()->notorm->dynamic_like
				->where("dynamicid='{$dynamicid}'")
				->update(array("status"=>0));	

			DI()->notorm->dynamic
				->where("id='{$dynamicid}'")
				->update( array( 'isdel'=>1 ) );
		}				
		return 0;
	}	

	
	/* 举报 */
	public function report($data) {
		
		$dynamic=DI()->notorm->dynamic
					->select("uid")
					->where("id='{$data['dynamicid']}'")
					->fetchOne();
		if(!$dynamic){
			return 1000;
		}
		
		$data['touid']=$dynamic['uid'];
					
		$result= DI()->notorm->dynamic_report->insert($data);
		return 0;
	}

	/* 举报分类列表 */
	public function getReportlist() {
		
		$reportlist=DI()->notorm->dynamic_report_classify
					->select("*")
					->order("list_order asc")
					->fetchAll();
		if(!$reportlist){
			return 1001;
		}
		
		return $reportlist;
		
	}
	
	/* 获取动态信息 */
	public function getDynamic($uid,$dynamicid,$where='status=1') {
		$info=DI()->notorm->dynamic
                ->select('id,uid,title,thumb,video_thumb,href,voice,length,likes,comments,type')
				->where('id = ?',$dynamicid)
                ->where($where)
				->fetchOne();
		if($info){
			$info=handleDynamic($uid,$info);
		}
		return $info;
	}
	
	
	/* 动态话题标签列表 */
	public function getDynamicLabels($p) {
		
		
		$where='1=1';
		$order='isrecommend desc, use_nums desc, orderno asc';
		$reportlist=getDynamicLabels($where,$order,$p,1);
		if(!$reportlist){
			return 1001;
		}
		
		
		foreach($reportlist as $k=>$v){
			$v['thumb']=get_upload_path($v['thumb']);
			$v['name']='#'.$v['name'];
			$v['use_nums_msg']=$v['use_nums'].'人参与了该话题';
			$reportlist[$k]=$v;
		}
		return $reportlist;
		
	}
	
	
	/* 热门话题标签-前10个 */
	public function getHotDynamicLabels() {

					
		$where='1=1';
		$order='isrecommend desc, use_nums desc, orderno asc';
		$reportlist=getDynamicLabels($where,$order,10,0);
					
		if(!$reportlist){
			return 1001;
		}
		
		
		foreach($reportlist as $k=>$v){
			$v['thumb']=get_upload_path($v['thumb']);
			$v['name']='#'.$v['name'];
			$v['use_nums_msg']=$v['use_nums'].'人参与了该话题';
			$reportlist[$k]=$v;
		}
		return $reportlist;
		
	}
	
	
	/* 获取热门话题下的动态 */
	public function getLabelDynamic($uid,$labelid,$p){
		
		 if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;

		$where="labelid={$labelid} and isdel=0 and status=1";
		$dynamic=DI()->notorm->dynamic
				->select("*")
				->where($where)
				->order("addtime desc")
				->limit($start,$nums)
				->fetchAll();
		
		if(!$dynamic){
				return array();
		}
	
		foreach($dynamic as $k=>$v){
				$v=handleDynamic($uid,$v);
                $dynamic[$k]=$v;
		}		
		
		return $dynamic;
		
	}
	
	
	/*搜索界面话题标签-前5个 */
	public function searchHotLabels() {
		
			
		$where='isrecommend=1';
		$order='use_nums desc, orderno asc';
		$reportlist=getDynamicLabels($where,$order,5,0);			
		
		if(!$reportlist){
			return 1001;
		}
		
		
		foreach($reportlist as $k=>$v){
			$v['thumb']=get_upload_path($v['thumb']);
			$v['name']='#'.$v['name'];
			$v['use_nums_msg']=$v['use_nums'].'人参与了该话题';
			$reportlist[$k]=$v;
		}
		return $reportlist;
		
	}
	
	
	/*搜索话题 */
	public function searchLabels($key,$p) {
		
        $where=" name like '%{$key}%'";
		$order='isrecommend desc, use_nums desc, orderno asc';
		$reportlist=getDynamicLabels($where,$order,$p,1);	

		if(!$reportlist){
			return [];
		}

		foreach($reportlist as $k=>$v){
			$v['thumb']=get_upload_path($v['thumb']);
			$v['name']='#'.$v['name'];
			$v['use_nums_msg']=$v['use_nums'].'人参与了该话题';
			$reportlist[$k]=$v;
		}
		return $reportlist;
    }
	
	
	/*删除评论 删除子级评论*/
	public function delComments($uid,$dynamicid,$commentid,$commentuid) {
       $result=DI()->notorm->dynamic
					->select("uid")
					->where("id='{$dynamicid}'")
					->fetchOne();	
					
		if(!$result){
			return 1001;
		}			

		if($uid!=$commentuid){
			if($uid!=$result['uid']){
				return 1002;
			}
		}
		// 删除 评论记录
		DI()->notorm->dynamic_comments
					->where("id='{$commentid}'")
					->delete(); 
		//删除视频评论喜欢
		DI()->notorm->dynamic_comments_like
					->where("commentid='{$commentid}'")
					->delete(); 
		/* 更新 视频 */
		DI()->notorm->dynamic
            ->where("id = '{$dynamicid}' and comments>0")
		 	->update( array('comments' => new NotORM_Literal("comments - 1") ) );
		
		
		//删除相关的子级评论
		$lists=DI()->notorm->dynamic_comments
				->select("*")
				->where("commentid='{$commentid}' or parentid='{$commentid}'")
				->fetchAll();
		foreach($lists as $k=>$v){
			//删除 评论记录
			DI()->notorm->dynamic_comments
						->where("id='{$v['id']}'")
						->delete(); 
			//删除视频评论喜欢
			DI()->notorm->dynamic_comments_like
						->where("commentid='{$v['id']}'")
						->delete(); 
						
			/* 更新 视频 */
			DI()->notorm->dynamic
				->where("id = '{$v['dynamicid']}' and comments>0")
				->update( array('comments' => new NotORM_Literal("comments - 1") ) );
		}				
		return 0;

    }
	
	
	
	
	
}
