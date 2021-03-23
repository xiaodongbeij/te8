<?php

class Model_Video extends PhalApi_Model_NotORM {
	/* 发布视频 */
	public function setVideo($data,$music_id) {
		$uid=$data['uid'];

		$configPri=getConfigPri();

		if($configPri['video_audit_switch']==0){
			$data['status']=1;
		}  
		//视频分类是否存在
		if($data['classid']){
			$isexitclass=DI()->notorm->video_class->where("id=?",$data['classid'])->fetchOne();
			if(!$isexitclass){
				return 1007;//视频分类不存在
            }
		}

		$result= DI()->notorm->video->insert($data);

		if($music_id>0){ //更新背景音乐被使用次数
			DI()->notorm->music
            ->where("id = '{$music_id}'")
		 	->update( array('use_nums' => new NotORM_Literal("use_nums + 1") ) );
		}
		
		return $result;
	}	

	/* 评论/回复 */
    public function setComment($data) {
    	$videoid=$data['videoid'];

		/* 更新 视频 */
		DI()->notorm->video
            ->where("id = '{$videoid}'")
		 	->update( array('comments' => new NotORM_Literal("comments + 1") ) );


        DI()->notorm->video_comments
            ->insert($data);
			
		$videoinfo=DI()->notorm->video
					->select("comments")
					->where('id=?',$videoid)
					->fetchOne();
					
		$count=DI()->notorm->video_comments
					->where("commentid='{$data['commentid']}'")
					->count();
		$rs=array(
			'comments'=>$videoinfo['comments'],
			'replys'=>$count,
		);

		return $rs;	
    }			

	/* 阅读 */
	public function addView($uid,$videoid){
		/*$view=DI()->notorm->video_view
				->select("id")
				->where("uid='{$uid}' and videoid='{$videoid}'")
				->fetchOne();

		if(!$view){
			DI()->notorm->video_view
						->insert(array("uid"=>$uid,"videoid"=>$videoid,"addtime"=>time() ));
						
			DI()->notorm->video
				->where("id = '{$videoid}'")
				->update( array('view' => new NotORM_Literal("view + 1") ) );
		}*/

		/*//用户看过的视频存入redis中
		$readLists=DI()->redis -> Get('readvideo_'.$uid);
		$readArr=array();
		if($readLists){
			$readArr=json_decode($readLists,true);
			if(!in_array($videoid,$readArr)){
				$readArr[]=$videoid;
			}
		}else{
			$readArr[]=$videoid;
		}

		DI()->redis -> Set('readvideo_'.$uid,json_encode($readArr));*/

		DI()->notorm->video
				->where("id = '{$videoid}'")
				->update( array('views' => new NotORM_Literal("views + 1") ) );

		return 0;
	}
	/* 点赞 */
	public function addLike($uid,$videoid){
		$rs=array(
			'islike'=>'0',
			'likes'=>'0',
		);
		$video=DI()->notorm->video
				->select("likes,uid,thumb")
				->where("id = '{$videoid}'")
				->fetchOne();

		if(!$video){
			return 1001;
		}
		if($video['uid']==$uid){
			return 1002;//不能给自己点赞
		}
		$like=DI()->notorm->video_like
						->select("id")
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->fetchOne();
		if($like){
			DI()->notorm->video_like
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->delete();
			
			DI()->notorm->video
				->where("id = '{$videoid}' and likes>0")
				->update( array('likes' => new NotORM_Literal("likes - 1") ) );
			$rs['islike']='0';
		}else{
			DI()->notorm->video_like
						->insert(array("uid"=>$uid,"videoid"=>$videoid,"addtime"=>time() ));
			
			DI()->notorm->video
				->where("id = '{$videoid}'")
				->update( array('likes' => new NotORM_Literal("likes + 1") ) );
			$rs['islike']='1';
		}	
		
		$video=DI()->notorm->video
				->select("likes,uid,thumb")
				->where("id = '{$videoid}'")
				->fetchOne();
				
		$rs['likes']=$video['likes'];
		
		return $rs; 		
	}

	/* 踩 */
	public function addStep($uid,$videoid){
		$rs=array(
			'isstep'=>'0',
			'steps'=>'0',
		);
		$like=DI()->notorm->video_step
						->select("id")
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->fetchOne();
		if($like){
			DI()->notorm->video_step
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->delete();
			
			DI()->notorm->video
				->where("id = '{$videoid}' and steps>0")
				->update( array('steps' => new NotORM_Literal("steps - 1") ) );
			$rs['isstep']='0';
		}else{
			DI()->notorm->video_step
						->insert(array("uid"=>$uid,"videoid"=>$videoid,"addtime"=>time() ));
			
			DI()->notorm->video
				->where("id = '{$videoid}'")
				->update( array('steps' => new NotORM_Literal("steps + 1") ) );
			$rs['isstep']='1';
		}	
		
		$video=DI()->notorm->video
				->select("steps")
				->where("id = '{$videoid}'")
				->fetchOne();
		$rs['steps']=$video['steps'];
		return $rs; 		
	}

	/* 分享 */
	public function addShare($uid,$videoid){

		
		$rs=array(
			'isshare'=>'0',
			'shares'=>'0',
		);
		DI()->notorm->video
			->where("id = '{$videoid}'")
			->update( array('shares' => new NotORM_Literal("shares + 1") ) );
		$rs['isshare']='1';

		
		$video=DI()->notorm->video
				->select("shares")
				->where("id = '{$videoid}'")
				->fetchOne();
		$rs['shares']=$video['shares'];
		
		return $rs; 		
	}

	/* 拉黑视频 */
	public function setBlack($uid,$videoid){
		$rs=array(
			'isblack'=>'0',
		);
		$like=DI()->notorm->video_black
						->select("id")
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->fetchOne();
		if($like){
			DI()->notorm->video_black
						->where("uid='{$uid}' and videoid='{$videoid}'")
						->delete();
			$rs['isshare']='0';
		}else{
			DI()->notorm->video_black
						->insert(array("uid"=>$uid,"videoid"=>$videoid,"addtime"=>time() ));
			$rs['isshare']='1';
		}	
		return $rs; 		
	}


	/* 评论/回复 点赞 */
	public function addCommentLike($uid,$commentid){
		$rs=array(
			'islike'=>'0',
			'likes'=>'0',
		);

		//根据commentid获取对应的评论信息
		$commentinfo=DI()->notorm->video_comments
			->where("id='{$commentid}'")
			->fetchOne();

		if(!$commentinfo){
			return 1001;
		}

		$like=DI()->notorm->video_comments_like
			->select("id")
			->where("uid='{$uid}' and commentid='{$commentid}'")
			->fetchOne();

		if($like){
			DI()->notorm->video_comments_like
						->where("uid='{$uid}' and commentid='{$commentid}'")
						->delete();
			
			DI()->notorm->video_comments
				->where("id = '{$commentid}' and likes>0")
				->update( array('likes' => new NotORM_Literal("likes - 1") ) );
			$rs['islike']='0';

		}else{
			DI()->notorm->video_comments_like
						->insert(array("uid"=>$uid,"commentid"=>$commentid,"addtime"=>time(),"touid"=>$commentinfo['uid'],"videoid"=>$commentinfo['videoid'] ));
			
			DI()->notorm->video_comments
				->where("id = '{$commentid}'")
				->update( array('likes' => new NotORM_Literal("likes + 1") ) );
			$rs['islike']='1';
		}	
		
		$video=DI()->notorm->video_comments
				->select("likes")
				->where("id = '{$commentid}'")
				->fetchOne();

		//获取视频信息
		$videoinfo=DI()->notorm->video->select("thumb")->where("id='{$commentinfo['videoid']}'")->fetchOne();

		$rs['likes']=$video['likes'];

		return $rs; 		
	}
	
	/* 热门视频 */
	public function getVideoList($uid,$p){

        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;

		$videoids_s='';
		$where="classid=7";  //上架且审核通过
		
		$video=DI()->notorm->video
				->select("*")
				->where($where)
				->order("RAND()")
				->limit($start,$nums)
				->fetchAll();

		return $video;
	}


	/* 关注人视频 */
	public function getAttentionVideo($uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
		
		$video=array();
		$attention=DI()->notorm->user_attention
				->select("touid")
				->where("uid='{$uid}'")
				->fetchAll();
		
		if($attention){
			
			$uids=array_column($attention,'touid');
			$touids=implode(",",$uids);
			
			$videoids_s=getVideoBlack($uid);
			$where="uid in ({$touids}) and id not in ({$videoids_s})  and isdel=0 and status=1";
			
			$video=DI()->notorm->video
					->select("*")
					->where($where)
					->order("addtime desc")
					->limit($start,$nums)
					->fetchAll();


			if(!$video){
				return 0;
			}
			
			foreach($video as $k=>$v){
				$v=handleVideo($uid,$v);
            
                $video[$k]=$v;
				
			}				
			
		}
		

		return $video;		
	} 			
	
	/* 视频详情 */
	public function getVideo($uid,$videoid){

	    $today = strtotime(date('Y-m-d', time()));

        $key = $uid . $videoid . $today;
        if($info = getcaches($key)){
            return $info;
        }

	    $user = DI()->notorm->user
            ->where("id = {$uid}")
            ->select('id,viewing_num,is_share,v_up_time')
            ->fetchOne();

	    $user_vip = DI()->notorm->vip_user
            ->where("uid = {$uid} and endtime > {$today}")
            ->fetchOne();

	    if($user['v_up_time'] == null || strtotime(date('Y-m-d', $user['v_up_time'])) != strtotime(date('Y-m-d', time()))) {
            $res = DI()->notorm->user
                ->where("id = ?",$uid)
                ->update([
                    'viewing_num' => 3,
                    'is_share' => 2,
                    'v_up_time' => strtotime(date('Y-m-d', time())),
                ]);
            if(!$res) return 1001;
            $user = DI()->notorm->user
                ->where("id = {$uid}")
                ->select('id,viewing_num,is_share,v_up_time')
                ->fetchOne();
        }
	    if($user['viewing_num'] <= 0 && $user['is_share'] == 2 && !$user_vip){
            return 1002;
        }

		$video=DI()->notorm->video
					->select("*")
					->where("id = {$videoid}")
					->fetchOne();
		if(!$video){
			return 1000;
		}
		
		$video=handleVideo($uid,$video);

		if(!$user_vip && $user['is_share'] == 2){
            $res = DI()->notorm->user
                ->where("id = {$uid}")
                ->update(['viewing_num' => $user['viewing_num'] - 1]);
            if(!$res) return 1003;
        }

		return $video;
	}
	
	/* 评论列表 */
	public function getComments($uid,$videoid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;

		$sql = "SELECT vc.addtime,vc.uid,vc.commentid,content,likes,user_nicename,avatar,IF($uid=vcl.uid,1,0) islike FROM cmf_video_comments vc LEFT JOIN cmf_user cu on vc.uid=cu.id LEFT JOIN cmf_video_comments_like vcl on vcl.commentid=vc.id where vc.videoid = $videoid order by vc.id desc limit $start,$nums ";
		$comments=DI()->notorm->video_comments->queryAll($sql);
		foreach($comments as $k=>$v){
			$comments[$k]['datetime']=datetime($v['addtime']);	
			$comments[$k]['likes']=NumberFormat($v['likes']);	
		}
		
		$commentnum=DI()->notorm->video_comments
					->where("videoid='{$videoid}'")
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
		$comments=DI()->notorm->video_comments
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
				$tocommentinfo=DI()->notorm->video_comments
					->select("content,at_info")
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
		$like=DI()->notorm->video_comments_like
				->select("id")
				->where("uid='{$uid}' and commentid='{$commentid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}
	
	/* 我的视频 */
	public function getMyVideo($uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;
		
		$video=DI()->notorm->video
				->select("*")
				->where('uid=?  and isdel=0',$uid)
				->order("addtime desc")
				->limit($start,$nums)
				->fetchAll();
		
		foreach($video as $k=>$v){
            
            $xiajia_reason=$v['xiajia_reason'];
			$v=handleVideo($uid,$v);
            $v['xiajia_reason']=$xiajia_reason;
            
            $video[$k]=$v;
			
		}

				
		return $video;
	} 	
	/* 删除视频 */
	public function del($uid,$videoid){
		
		$result=DI()->notorm->video
					->where("id='{$videoid}' and uid='{$uid}'")
					->update( array( 'isdel'=>1 ) );
		if($result){
			// 删除 评论记录
			 /*DI()->notorm->video_comments
						->where("videoid='{$videoid}'")
						->delete(); 
			//删除视频评论喜欢
			DI()->notorm->video_comments_like
						->where("videoid='{$videoid}'")
						->delete(); 
			
			// 删除  点赞
			 DI()->notorm->video_like
						->where("videoid='{$videoid}'")
						->delete(); 
			//删除视频举报
			DI()->notorm->video_report
						->where("videoid='{$videoid}'")
						->delete(); 
			// 删除视频 
			 DI()->notorm->video
						->where("id='{$videoid}'")
						->delete();	*/ 

			//将喜欢的视频列表状态修改
			DI()->notorm->video_like
				->where("videoid='{$videoid}'")
				->update(array("status"=>0));	
		}				
		return 0;
	}	

	/* 个人主页视频 */
	public function getHomeVideo($uid,$touid,$p){
        if($p<1){
            $p=1;
        }
		$nums=21;
		$start=($p-1)*$nums;
		
		
		if($uid==$touid){  //自己的视频（需要返回视频的状态前台显示）
			$where=" uid={$uid} and isdel='0' and is_ad=0";
		}else{  //访问其他人的主页视频
            $videoids_s=getVideoBlack($uid);
			$where="id not in ({$videoids_s}) and uid={$touid} and isdel='0' and status=1  and is_ad=0";
		}
		
		
		$video=DI()->notorm->video
				->select("*")
				->where($where)
				->order("addtime desc")
				->limit($start,$nums)
				->fetchAll();

		foreach($video as $k=>$v){
			$v=handleVideo($uid,$v);
            
            $video[$k]=$v;
		}			

		return $video;
		
	}
	/* 举报 */
	public function report($data) {
		
		$video=DI()->notorm->video
					->select("uid")
					->where("id='{$data['videoid']}'")
					->fetchOne();
		if(!$video){
			return 1000;
		}
		
		$data['touid']=$video['uid'];
					
		$result= DI()->notorm->video_report->insert($data);
		return 0;
	}	
	



	public function getRecommendVideos($uid,$p,$isstart){
        if($p<1){
            $p=1;
        }
		$pnums=20;
		$start=($p-1)*$pnums;


		
		$configPri=getConfigPri();
		$video_showtype=$configPri['video_showtype'];


		


		if($video_showtype==0){ //随机

			if($p==1){
				DI()->redis -> del('readvideo_'.$uid);
			}

			//去除看过的视频
			$where=array();
			$readLists=DI()->redis -> Get('readvideo_'.$uid);
			if($readLists){
				$where=json_decode($readLists,true);
			}

			$info=DI()->notorm->video
			->where("isdel=0 and status=1 and is_ad=0")
			->where('not id',$where)
			->order("rand()")
			->limit($pnums)
			
			->fetchAll();
			$where1=array();
			foreach ($info as $k => $v) {
				if(!in_array($v['id'],$where)){
					$where1[]=$v['id'];
				}
			}

			//将两数组合并
			$where2=array_merge($where,$where1);

			DI()->redis -> set('readvideo_'.$uid,json_encode($where2));



		}else{

			//获取私密配置里的评论权重和点赞权重
			$comment_weight=$configPri['comment_weight'];
			$like_weight=$configPri['like_weight'];
			$share_weight=$configPri['share_weight'];

			$prefix= DI()->config->get('dbs.tables.__default__.prefix');

			//热度值 = 点赞数*点赞权重+评论数*评论权重+分享数*分享权重
			//转化率 = 完整观看次数/总观看次数
			//排序规则：（曝光值+热度值）*转化率
			//曝光值从视频发布开始，每小时递减1，直到0为止


			/*废弃$info=DI()->notorm->video->queryAll("select *,format(watch_ok/views,2) as aaa, (ceil(comments *".$comment_weight." + likes *".$like_weight." + shares *".$share_weight.") )*format(watch_ok/views,2) as recomend from ".$prefix."video where isdel=0 and status=1  order by recomend desc,addtime desc limit ".$start.",".$pnums);*/

			$info=DI()->notorm->video
            ->select("*,(ceil(comments * ".$comment_weight." + likes * ".$like_weight." + shares * ".$share_weight."))* if(format(watch_ok/views,2) >1,'1',format(watch_ok/views,2)) as recomend")
            ->where("isdel=0 and status=1 and is_ad=0")
            // ->where('not id',$where)
            ->order("recomend desc,addtime desc")
            ->limit($start,$pnums)
            ->fetchAll();
		}


		if(!$info){
			return 1001;
		}

		foreach ($info as $k => $v) {
			$v=handleVideo($uid,$v);
            
            $info[$k]=$v;
		}


		return $info;
	}

	/*获取附近的视频*/
	public function getNearby($uid,$lng,$lat,$p){
        if($p<1){
            $p=1;
        }
		$pnum=20;
		$start=($p-1)*$pnum;

		$prefix= DI()->config->get('dbs.tables.__default__.prefix');

		$info=DI()->notorm->video->queryAll("select *, round(6378.138 * 2 * ASIN(SQRT(POW(SIN(( ".$lat." * PI() / 180 - lat * PI() / 180) / 2),2) + COS(".$lat." * PI() / 180) * COS(lat * PI() / 180) * POW(SIN((".$lng." * PI() / 180 - lng * PI() / 180) / 2),2))) * 1000) AS distance FROM ".$prefix."video  where uid !=".$uid." and isdel=0 and status=1  and is_ad=0 order by distance asc,addtime desc limit ".$start.",".$pnum);

		if(!$info){
			return 1001;
		}


		foreach ($info as $k => $v) {
            
            $v=handleVideo($uid,$v);
            $v['distance']=distanceFormat($v['distance']);
            
            $info[$k]=$v;
			
		}
		
		return $info;
	}

	/* 举报分类列表 */
	public function getReportContentlist() {
		
		$reportlist=DI()->notorm->video_report_classify
					->select("*")
					->order("list_order asc")
					->fetchAll();
		if(!$reportlist){
			return 1001;
		}
		
		return $reportlist;
		
	}

	/*更新视频看完次数*/
	public function setConversion($videoid){


		//更新视频看完次数
		$res=DI()->notorm->video
				->where("id = '{$videoid}' and isdel=0 and status=1")
				->update( array('watch_ok' => new NotORM_Literal("watch_ok + 1") ) );

		return 1;
	}	

	
	/* 分类视频 */
	public function getClassVideo($videoclassid,$uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=21;
		$start=($p-1)*$nums;
		$where="  classid={$videoclassid}";

		$video=DI()->notorm->video
				->select("*")
				->where($where)
				->order("addtime desc")
				->limit($start,$nums)
				->fetchAll();
				
		return $video;
		
	}
	
	/*删除评论 删除子级评论*/
	public function delComments($uid,$videoid,$commentid,$commentuid) {
       $result=DI()->notorm->video
					->select("uid")
					->where("id='{$videoid}'")
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
		DI()->notorm->video_comments
					->where("id='{$commentid}'")
					->delete(); 
		//删除视频评论喜欢
		DI()->notorm->video_comments_like
					->where("commentid='{$commentid}'")
					->delete(); 
		/* 更新 视频 */
		DI()->notorm->video
            ->where("id = '{$videoid}' and comments>0")
		 	->update( array('comments' => new NotORM_Literal("comments - 1") ) );
		
		
		//删除相关的子级评论
		$lists=DI()->notorm->video_comments
				->select("*")
				->where("commentid='{$commentid}' or parentid='{$commentid}'")
				->fetchAll();
		foreach($lists as $k=>$v){
			//删除 评论记录
			DI()->notorm->video_comments
						->where("id='{$v['id']}'")
						->delete(); 
			//删除视频评论喜欢
			DI()->notorm->video_comments_like
						->where("commentid='{$v['id']}'")
						->delete(); 
						
			/* 更新 视频 */
			DI()->notorm->video
				->where("id = '{$v['videoid']}' and comments>0")
				->update( array('comments' => new NotORM_Literal("comments - 1") ) );
		}
			
		
						
		return 0;

    }

}
