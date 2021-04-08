<?php
/**
 * 搜索
 */
class Api_Search extends PhalApi_Api {

    public function getRules() {
        return array(
            'getLvList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'key' => array('name' => 'key', 'type' => 'string','require' => true, 'desc' => '搜索关键词'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
        );
    }

    /**
     * NEW搜索
     * @desc 用于 直播，视频搜索
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['live'] 直播
     * @return string info['video'] 视频
     * @return string msg 提示信息
     */
    public function getLvList() {
  
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $key=checkNull($this->key);
        $p=checkNull($this->p);
        if(!$p){
            $p=1;
        }
        $nums=21;
        $start=($p-1)*$nums;

        if($key==''){
            $rs['code']=1001;
            $rs['msg']='请输入您要搜索的主播昵称或视频名称';
            return $rs;
        }

        $users=DI()->notorm->user
            ->select('id')
            ->where('user_type = 2 and id != ?', $uid)
            ->where("id = ? or user_nicename like ?",$key,"%{$key}%")
            ->limit($start,$nums)
            ->fetchAll();

        if($users){
            $uids=array_column($users,'id');
            $uids_s=implode(',',$uids);
            $where="islive = 1 and uid != {$uid} and uid in ({$uids_s})";
//            $domain = new Domain_Livepk();
//            $lives = $domain->getLiveList($uid,$where,$p);
//            if($lives) {
//                foreach($lives as $k=>$v){
//                    $userinfo=getUserInfo($v['uid']);
//                    $v['level']=$userinfo['level'];
//                    $v['level_anchor']=$userinfo['level_anchor'];
//                    $v['sex']=$userinfo['sex'];
//                    $lives[$k]=$v;
//                }
//            }
            $result=DI()->notorm->live
                ->select('uid,title,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,language,game_action,show_name,short_name,c_id,c_type,hot,icon')
                ->where($where)
                ->order("starttime desc")
                ->limit($start,$nums)
                ->fetchAll();

            foreach($result as $k=>$v){

                $v=handleLive($v);

                $result[$k]=$v;
            }

            if($result){
                $last=end($result);
                $_SESSION['follow_starttime']=$last['starttime'];
            }
        }



        $video=DI()->notorm->video
            ->select("*")
            ->where("isdel='0' and status=1  and title LIKE ?", "%{$key}%")
            ->order("addtime desc")
            ->limit($start,$nums)
            ->fetchAll();
        if($video){
            foreach($video as $k=>$v){
                $v=handleVideo($uid,$v);
                $video[$k]=$v;
            }
        }

        if(!$result && !$video) return $rs;

        $rs['info']['live'] = $result;
        $rs['info']['video'] = $video;
        return $rs;
    }
}
