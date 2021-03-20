<?php
/**
 * 贡献榜
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class ContributeController extends HomebaseController {
	
	function index(){
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '0';
        $uid=(int)checkNull($uid);
        
        $nowtime=time();
        //当天0点
        $today=date("Ymd",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");

        $w=date('w',$nowtime); 
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
        $first=1;
        //周一
        $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
        $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

        //本周结束日期 
        //周天
        $week_end=strtotime("{$week} +1 week");

        
        //本月第一天
        $month=date("Y-m",$nowtime).'-01';
        $month_start=strtotime($month);

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");
        
            
            
		$p=1;
		$page_nums=20;
		$start=($p-1)*$page_nums;
        
        /* 日榜 */
		$list_day=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
		foreach($list_day as $k=>$v){
			$list_day[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_day",$list_day);
        
        $list_day_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->sum('total');
        if(!$list_day_total){
            $list_day_total=0;
        }
        
        $this->assign("list_day_total",$list_day_total);
        
        /* 周榜 */
        $list_week=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
		foreach($list_week as $k=>$v){
			$list_week[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_week",$list_week);
        
        $list_week_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->sum('total');
        if(!$list_week_total){
            $list_week_total=0;
        }
        $this->assign("list_week_total",$list_week_total);
        
        /* 月榜 */
        $list_month=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
		foreach($list_month as $k=>$v){
			$list_month[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_month",$list_month);
        
        $list_month_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->sum('total');
        if(!$list_month_total){
            $list_month_total=0;
        }
        $this->assign("list_month_total",$list_month_total);
        
        /* 总榜 */
        $list_all=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
		foreach($list_all as $k=>$v){
			$list_all[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_all",$list_all);
        
        $list_all_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' ")->sum('total');
        if(!$list_all_total){
            $list_all_total=0;
        }
        $this->assign("list_all_total",$list_all_total);
        
		$this->assign("uid",$uid);
		
		return $this->fetch();	
	}

	public function gift(){
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '0';
        $uid=(int)checkNull($uid);

        $nowtime=time();
        //当天0点
        $today=date("Ymd",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");

        $w=date('w',$nowtime);
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $first=1;
        //周一
        $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'));
        $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days');

        //本周结束日期
        //周天
        $week_end=strtotime("{$week} +1 week");


        //本月第一天
        $month=date("Y-m",$nowtime).'-01';
        $month_start=strtotime($month);

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");



        $p=1;
        $page_nums=20;
        $start=($p-1)*$page_nums;

        /* 日榜 */
        $list_day=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
        foreach($list_day as $k=>$v){
            $list_day[$k]['userinfo']=getUserInfo($v['uid']);
        }
        $this->assign("list_day",$list_day);

        $list_day_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->sum('total');
        if(!$list_day_total){
            $list_day_total=0;
        }

        $this->assign("list_day_total",$list_day_total);

        /* 周榜 */
        $list_week=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
        foreach($list_week as $k=>$v){
            $list_week[$k]['userinfo']=getUserInfo($v['uid']);
        }
        $this->assign("list_week",$list_week);

        $list_week_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->sum('total');
        if(!$list_week_total){
            $list_week_total=0;
        }
        $this->assign("list_week_total",$list_week_total);

        /* 月榜 */
        $list_month=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
        foreach($list_month as $k=>$v){
            $list_month[$k]['userinfo']=getUserInfo($v['uid']);
        }
        $this->assign("list_month",$list_month);

        $list_month_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->sum('total');
        if(!$list_month_total){
            $list_month_total=0;
        }
        $this->assign("list_month_total",$list_month_total);

        /* 总榜 */
        $list_all=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
        foreach($list_all as $k=>$v){
            $list_all[$k]['userinfo']=getUserInfo($v['uid']);
        }
        $this->assign("list_all",$list_all);

        $list_all_total=Db::name('user_voterecord')->where(" action in (1,2) and uid='{$uid}' ")->sum('total');
        if(!$list_all_total){
            $list_all_total=0;
        }
        $this->assign("list_all_total",$list_all_total);

        $this->assign("uid",$uid);

        return json([
            'list_day' => $list_day,
            'list_day_total' => $list_day_total,
            'list_week' => $list_week,
            'list_week_total' => $list_week_total,
            'list_month' => $list_month_total,
            'list_month_total' => $list_month_total,
            'list_all' => $list_all,
            'list_all_total' => $list_all_total
        ]);
    }
	
	public function order(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '0';
        $type=isset($data['type']) ? $data['type']: '';
        $uid=(int)checkNull($uid);
        $type=checkNull($type);
		
		if($type=='week'){
			
			$nowtime=time();
			//当天0点
			//$today=date("Ymd",$nowtime);
			//$today_start=strtotime($today);
			//当天 23:59:59
			//$today_end=strtotime("{$today} + 1 day")-1;

			$w=date('w',$nowtime); 
			//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
			$first=1;
			//周一
			$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
			$week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

			//本周结束日期 
			//周天
			$week_end=strtotime("{$week} +1 week")-1;
			
			
			$list=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime>{$week_start} and addtime<{$week_end}")->group("fromid")->order("total desc")->limit(0,20)->select()->toArray();
			
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}else{
			$list=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}'")->group("fromid")->order("total desc")->limit(0,20)->select()->toArray();
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}

		$this->assign("list",$list);


		return $this->fetch();
		
	}

}