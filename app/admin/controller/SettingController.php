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
namespace app\admin\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;

use think\Db;
use think\Request;

/**
 * Class SettingController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   =>'设置',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 0,
 *     'icon'   =>'cogs',
 *     'remark' =>'系统设置入口'
 * )
 */
class SettingController extends AdminBaseController
{

    /**
     * APP设置内容
     *
     *
    */
    public function app_set(Request $request)
    {
        if($request->isPost()){
            $data['community_convention'] = $request->post('community_convention', '');
            $data['privacy_policy'] = $request->post('privacy_policy', '');
            $data['clause'] = $request->post('clause', '');
            $data['contact_us'] = $request->post('contact_us', '');

            $data = json_encode($data);

            $res = Db::name('option')->where('id', 10)->update(['option_value' => $data]);

            $this->success('ok');
        }
        $info = Db::name('option')->where('id', 10)->find();
        $val = json_decode($info['option_value'], true);
        return $this->fetch('', ['info' => $val]);
    }

    public function machine_set(Request $request)
    {
        if($request->isPost()){
            $data['approach_frequency'] = $request->post('approach_frequency', '');
            $data['chat_frequency'] = $request->post('chat_frequency', '');
            $data['focus_frequency'] = $request->post('focus_frequency', '');
            $data['gift_frequency'] = $request->post('gift_frequency', '');
            $data['gift_id'] = $request->post('gift_id', '');
            $data['open_coin'] = $request->post('open_coin', '');
            $data['min_grade'] = $request->post('min_grade', '');
            $data['max_grade'] = $request->post('max_grade', '');
            $data['max_people'] = $request->post('max_people', '');
            
            
            $this->resetcache('getMachineSet',$data);
            $data = json_encode($data);


            $res = Db::name('option')->where('id', 11)->update(['option_value' => $data]);

            $this->success('ok');
        }
        $info = Db::name('option')->where('id', 11)->find();
        $val = json_decode($info['option_value'], true);
        return $this->fetch('', ['info' => $val]);
    }

    /**
     * 网站信息
     * @adminMenu(
     *     'name'   => '网站信息',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '网站信息',
     *     'param'  => ''
     * )
     */
    public function site()
    {
        $content = hook_one('admin_setting_site_view');

        if (!empty($content)) {
            return $content;
        }

        $noNeedDirs     = [".", "..", ".svn", 'fonts'];
        $adminThemesDir = WEB_ROOT . config('template.cmf_admin_theme_path') . config('template.cmf_admin_default_theme') . '/public/assets/themes/';
        $adminStyles    = cmf_scan_dir($adminThemesDir . '*', GLOB_ONLYDIR);
        $adminStyles    = array_diff($adminStyles, $noNeedDirs);
        $cdnSettings    = cmf_get_option('cdn_settings');
        $cmfSettings    = cmf_get_option('cmf_settings');
        $adminSettings  = cmf_get_option('admin_settings');

        $adminThemes = [];
        $themes      = cmf_scan_dir(WEB_ROOT . config('template.cmf_admin_theme_path') . '/*', GLOB_ONLYDIR);

        foreach ($themes as $theme) {
            if (strpos($theme, 'admin_') === 0) {
                array_push($adminThemes, $theme);
            }
        }

        if (APP_DEBUG && false) { // TODO 没确定要不要可以设置默认应用
            $apps = cmf_scan_dir(APP_PATH . '*', GLOB_ONLYDIR);
            $apps = array_diff($apps, $noNeedDirs);
            $this->assign('apps', $apps);
        }
        
        $this->assign('site_info', cmf_get_option('site_info'));
        $this->assign("admin_styles", $adminStyles);
        $this->assign("templates", []);
        $this->assign("admin_themes", $adminThemes);
        $this->assign("cdn_settings", $cdnSettings);
        $this->assign("admin_settings", $adminSettings);
        $this->assign("cmf_settings", $cmfSettings);

        return $this->fetch();
    }

    /**
     * 网站信息设置提交
     * @adminMenu(
     *     'name'   => '网站信息设置提交',
     *     'parent' => 'site',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '网站信息设置提交',
     *     'param'  => ''
     * )
     */
    public function sitePost()
    {
        if ($this->request->isPost()) {
            $result = $this->validate($this->request->param(), 'SettingSite');
            if ($result !== true) {
                $this->error($result);
            }
            
            $oldconfig=cmf_get_option('site_info');
            
            $options = $this->request->param('options/a');
            
            $login_type=isset($_POST['login_type'])?$_POST['login_type']:'';
            $share_type=isset($_POST['share_type'])?$_POST['share_type']:'';
            $live_type=isset($_POST['live_type'])?$_POST['live_type']:'';
            
            $options['login_type']='';
            $options['share_type']='';
            $options['live_type']='';
  
         
            if($login_type){
                $options['login_type']=implode(',',$login_type);
            }
            
            if($share_type){
                $options['share_type']=implode(',',$share_type);
            }
            if($live_type){
                $options['live_type']=implode(',',$live_type);
            }
            
            cmf_set_option('site_info', $options,true);
            
            $this->resetcache('getConfigPub',$options);

            $cmfSettings = $this->request->param('cmf_settings/a');

            $bannedUsernames                 = preg_replace("/[^0-9A-Za-z_\\x{4e00}-\\x{9fa5}-]/u", ",", $cmfSettings['banned_usernames']);
            $cmfSettings['banned_usernames'] = $bannedUsernames;
            cmf_set_option('cmf_settings', $cmfSettings,true);
        
            $cdnSettings = $this->request->param('cdn_settings/a');
            cmf_set_option('cdn_settings', $cdnSettings,true);

            $adminSettings = $this->request->param('admin_settings/a');

            $routeModel = new RouteModel();
            if (!empty($adminSettings['admin_password'])) {
                $routeModel->setRoute($adminSettings['admin_password'] . '$', 'admin/Index/index', [], 2, 5000);
            } else {
                $routeModel->deleteRoute('admin/Index/index', []);
            }

            $routeModel->getRoutes(true);

            if (!empty($adminSettings['admin_theme'])) {
                $result = cmf_set_dynamic_config([
                    'template' => [
                        'cmf_admin_default_theme' => $adminSettings['admin_theme']
                    ]
                ]);

                if ($result === false) {
                    $this->error('配置写入失败!');
                }
            }

            cmf_set_option('admin_settings', $adminSettings,true);
            
            $action="修改公共配置 ";
			
			if($options['maintain_switch'] !=$oldconfig['maintain_switch']){
                $maintain_switch=$options['maintain_switch']?'开':'关';
                $action.='网站维护 '.$maintain_switch.' ';
            }
			
			if($options['site_name'] !=$oldconfig['site_name']){
                $action.='网站名称 '.$options['site_name'].' ';
            }
			
			if($options['site'] !=$oldconfig['site']){
                $action.='网站域名 '.$options['site'].' ';
            }
			
			if($options['name_coin'] !=$oldconfig['name_coin']){
                $action.='钻石名称 '.$options['name_coin'].' ';
            }
			
			if($options['name_score'] !=$oldconfig['name_score']){
                $action.='积分名称 '.$options['name_score'].' ';
            }
			
			if($options['name_votes'] !=$oldconfig['name_votes']){
                $action.='映票名称 '.$options['name_votes'].' ';
            }
			
			
            if($options['isup'] !=$oldconfig['isup']){
                $isup=$options['isup']?'开':'关';
                $action.='修改强制更新 '.$isup.' ';
            }
            
            if($options['isup'] !=$oldconfig['isup']){
                $isup=$options['isup']?'开':'关';
                $action.='修改强制更新 '.$isup.' ';
            }
            
   
            
            if($options['app_notice'] !=$oldconfig['app_notice']){
                
                $action.='修改首页弹窗内容 '.$options['app_notice'].' ';
            }
            if($options['apk_ver'] !=$oldconfig['apk_ver']){
                $action.='修改APK版本号 '.$options['apk_ver'].' ';
            }
            if($options['apk_url'] !=$oldconfig['apk_url']){
                $action.='修改APK下载链接 ';
            }
            
            if($options['ipa_ver'] !=$oldconfig['ipa_ver']){
                $action.='修改IPA版本号 '.$options['ipa_ver'].' ';
            }
            
			
			if($options['ipa_url'] !=$oldconfig['ipa_url']){
                $action.='修改IPA下载链接 '.$options['ipa_url'].' ';
            }
			
            if($options['login_type'] !=$oldconfig['login_type']){
                $action.='修改登录方式 ';
                $old_l=explode(',',$oldconfig['login_type']);
                $new_l=explode(',',$options['login_type']);
                foreach($old_l as $k=>$v){
                    if(!in_array($v,$new_l)){
                        $action.='关闭'.$v.' ';
                    }
                }
                
                foreach($new_l as $k=>$v){
                    if(!in_array($v,$old_l)){
                        $action.='开启'.$v.' ';
                    }
                }
            }
            // if($options['share_type'] !=$oldconfig['share_type']){
            //     $action.='修改分享方式 ';
                
            //     $old_l=explode(',',$oldconfig['share_type']);
            //     $new_l=explode(',',$options['share_type']);
            //     foreach($old_l as $k=>$v){
            //         if(!in_array($v,$new_l)){
            //             $action.='关闭'.$v.' ';
            //         }
            //     }
                
            //     foreach($new_l as $k=>$v){
            //         if(!in_array($v,$old_l)){
            //             $action.='开启'.$v.' ';
            //         }
            //     }
            // }
			
			
// 			if($options['wx_siteurl'] !=$oldconfig['wx_siteurl']){
//                 $action.='修改微信推广域名 '.$options['wx_siteurl'].' ';
//             }
			
// 			if($options['share_title'] !=$oldconfig['share_title']){
//                 $action.='修改直播分享标题 '.$options['share_title'].' ';
//             }
			
// 			if($options['share_des'] !=$oldconfig['share_des']){
//                 $action.='修改直播分享话术 '.$options['share_des'].' ';
//             }
			
			if($options['app_android'] !=$oldconfig['app_android']){
                $action.='修改AndroidAPP下载链接 '.$options['app_android'].' ';
            }
			
// 			if($options['app_ios'] !=$oldconfig['app_ios']){
//                 $action.='修改IOSAPP下载链接 '.$options['app_ios'].' ';
//             }
			
// 			if($options['video_share_title'] !=$oldconfig['video_share_title']){
//                 $action.='修改短视频分享标题 '.$options['video_share_title'].' ';
//             }
			
// 			if($options['video_share_des'] !=$oldconfig['video_share_des']){
//                 $action.='修改短视频分享话术 '.$options['video_share_des'].' ';
//             }
			
			
            
            if($options['live_type'] !=$oldconfig['live_type']){
                $action.='修改房间类型 ';
                
                $old_l=explode(',',$oldconfig['live_type']);
                $new_l=explode(',',$options['live_type']);
                foreach($old_l as $k=>$v){
                    if(!in_array($v,$new_l)){
                        $action.='关闭'.$v.' ';
                    }
                }
                
                foreach($new_l as $k=>$v){
                    if(!in_array($v,$old_l)){
                        $action.='开启'.$v.' ';
                    }
                }
            }
            // if($options['live_time_coin'] !=$oldconfig['live_time_coin']){
            //     $action.='修改计时直播收费 ';
            // }
			
			/*if($options['sprout_isp'] !=$oldconfig['sprout_isp']){
				
				if($options['sprout_isp']==2){
					$sprout_isp="相芯";
				}else if($options['sprout_isp']==1){
					$sprout_isp="美狐";
				}else{
					$sprout_isp="无";
				}
				
				
                $action.='修改萌颜服务商 '.$sprout_isp.' ';
            }*/
			
			if($options['sprout_key'] !=$oldconfig['sprout_key']){
                $action.='修改萌颜授权码-Andriod '.$options['sprout_key'].' ';
            }
			
			if($options['sprout_key_ios'] !=$oldconfig['sprout_key_ios']){
                $action.='修改萌颜授权码-IOS '.$options['sprout_key_ios'].' ';
            }
			
			if($options['skin_whiting'] !=$oldconfig['skin_whiting']){
                $action.='修改美颜-美白 '.$options['skin_whiting'].' ';
            }
			
			if($options['skin_smooth'] !=$oldconfig['skin_smooth']){
                $action.='修改美颜-磨皮 '.$options['skin_smooth'].' ';
            }
			
			if($options['skin_tenderness'] !=$oldconfig['skin_tenderness']){
                $action.='修改美颜-红润 '.$options['skin_tenderness'].' ';
            }
			
			if($options['eye_brow'] !=$oldconfig['eye_brow']){
                $action.='修改磨皮默认值-眉毛 '.$options['eye_brow'].' ';
            }
			if($options['big_eye'] !=$oldconfig['big_eye']){
                $action.='修改磨皮默认值-大眼 '.$options['big_eye'].' ';
            }
			if($options['eye_length'] !=$oldconfig['eye_length']){
                $action.='修改磨皮默认值-眼距 '.$options['eye_length'].' ';
            }
			
			if($options['eye_corner'] !=$oldconfig['eye_corner']){
                $action.='修改磨皮默认值-眼角 '.$options['eye_corner'].' ';
            }
			
			if($options['eye_alat'] !=$oldconfig['eye_alat']){
                $action.='修改磨皮默认值-开眼角 '.$options['eye_alat'].' ';
            }
			
			if($options['face_lift'] !=$oldconfig['face_lift']){
                $action.='修改磨皮默认值-瘦脸 '.$options['face_lift'].' ';
            }
			
			if($options['face_shave'] !=$oldconfig['face_shave']){
                $action.='修改磨皮默认值-削脸 '.$options['face_shave'].' ';
            }
			
			if($options['mouse_lift'] !=$oldconfig['mouse_lift']){
                $action.='修改磨皮默认值-嘴形 '.$options['mouse_lift'].' ';
            }
			if($options['nose_lift'] !=$oldconfig['nose_lift']){
                $action.='修改磨皮默认值-瘦鼻 '.$options['nose_lift'].' ';
            }
			if($options['chin_lift'] !=$oldconfig['chin_lift']){
                $action.='修改磨皮默认值-下巴 '.$options['chin_lift'].' ';
            }
			if($options['forehead_lift'] !=$oldconfig['forehead_lift']){
                $action.='修改磨皮默认值-额头 '.$options['forehead_lift'].' ';
            }
			if($options['lengthen_noseLift'] !=$oldconfig['lengthen_noseLift']){
                $action.='修改磨皮默认值-长鼻 '.$options['lengthen_noseLift'].' ';
            }
// 			if($options['payment_des'] !=$oldconfig['payment_des']){
//                 $action.='修改付费内容申请说明 '.$options['payment_des'].' ';
//             }
// 			if($options['payment_time'] !=$oldconfig['payment_time']){
//                 $action.='修改申请付费内容间隔天数(天) '.$options['payment_time'].' ';
//             }
// 			if($options['payment_percent'] !=$oldconfig['payment_percent']){
//                 $action.='修改付费内容默认抽水比例 '.$options['payment_percent'].' ';
//             }
// 			if($options['login_alert_title'] !=$oldconfig['login_alert_title']){
//                 $action.='修改弹框标题 '.$options['login_alert_title'].' ';
//             }
// 			if($options['login_alert_content'] !=$oldconfig['login_alert_content']){
//                 $action.='修改弹框内容 '.$options['login_alert_content'].' ';
//             }
// 			if($options['login_clause_title'] !=$oldconfig['login_clause_title']){
//                 $action.='修改APP登录界面底部协议标题 '.$options['login_clause_title'].' ';
//             }
// 			if($options['login_private_title'] !=$oldconfig['login_private_title']){
//                 $action.='修改隐私政策名称 '.$options['login_private_title'].' ';
//             }
// 			if($options['login_private_url'] !=$oldconfig['login_private_url']){
//                 $action.='修改隐私政策跳转链接 '.$options['login_private_url'].' ';
//             }
// 			if($options['login_service_title'] !=$oldconfig['login_service_title']){
//                 $action.='修改服务协议名称 '.$options['login_service_title'].' ';
//             }
// 			if($options['login_service_url'] !=$oldconfig['login_service_url']){
//                 $action.='修改服务协议跳转链接 '.$options['login_service_url'].' ';
//             }
			
		
		   
			if($action!='修改公共配置 '){
				setAdminLog($action);
			}
			
			
            $this->success("保存成功！", '');

        }
    }

    /**
     * 密码修改
     * @adminMenu(
     *     'name'   => '密码修改',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改',
     *     'param'  => ''
     * )
     */
    public function password()
    {
        return $this->fetch();
    }

    /**
     * 密码修改提交
     * @adminMenu(
     *     'name'   => '密码修改提交',
     *     'parent' => 'password',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改提交',
     *     'param'  => ''
     * )
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();
            if (empty($data['old_password'])) {
                $this->error("原始密码不能为空！");
            }
            if (empty($data['password'])) {
                $this->error("新密码不能为空！");
            }

            $userId = cmf_get_current_admin_id();

            $admin = Db::name('user')->where("id", $userId)->find();

            $oldPassword = $data['old_password'];
            $password    = $data['password'];
            $rePassword  = $data['re_password'];

            if (cmf_compare_password($oldPassword, $admin['user_pass'])) {
                if ($password == $rePassword) {

                    if (cmf_compare_password($password, $admin['user_pass'])) {
                        $this->error("新密码不能和原始密码相同！");
                    } else {
                        Db::name('user')->where('id', $userId)->update(['user_pass' => cmf_password($password)]);
                        $this->success("密码修改成功！");
                    }
                } else {
                    $this->error("密码输入不一致！");
                }

            } else {
                $this->error("原始密码不正确！");
            }
        }
    }

    /**
     * 上传限制设置界面
     * @adminMenu(
     *     'name'   => '上传设置',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置',
     *     'param'  => ''
     * )
     */
    public function upload()
    {
        $uploadSetting = cmf_get_upload_setting();
        $this->assign('upload_setting', $uploadSetting);
        return $this->fetch();
    }

    /**
     * 上传限制设置界面提交
     * @adminMenu(
     *     'name'   => '上传设置提交',
     *     'parent' => 'upload',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置提交',
     *     'param'  => ''
     * )
     */
    public function uploadPost()
    {
        if ($this->request->isPost()) {
            //TODO 非空验证
            $uploadSetting = $this->request->post();

			$olduploadSetting = cmf_get_upload_setting();
			
			cmf_set_option('upload_setting', $uploadSetting,true);
			
			$action="修改上传设置 ";
					
			
			if($uploadSetting['max_files'] !=$olduploadSetting['max_files']){
				$action.='最大同时上传文件数 '.$uploadSetting['max_files'].' ';
			}
			
			if($uploadSetting['chunk_size'] !=$olduploadSetting['chunk_size']){
				$action.='文件分块上传分块大小 '.$uploadSetting['chunk_size'].' ';
			}
			
			if($uploadSetting['file_types']['image']['upload_max_filesize'] !=$olduploadSetting['file_types']['image']['upload_max_filesize'] || $uploadSetting['file_types']['image']['extensions'] !=$olduploadSetting['file_types']['image']['extensions']){
				$action.='图片文件 大小: '.$uploadSetting['file_types']['image']['upload_max_filesize'].' 扩展名: '.$uploadSetting['file_types']['image']['extensions'].'  ';
			}
			
			if($uploadSetting['file_types']['video']['upload_max_filesize'] !=$olduploadSetting['file_types']['video']['upload_max_filesize'] || $uploadSetting['file_types']['video']['extensions'] !=$olduploadSetting['file_types']['video']['extensions']){
				$action.='视频文件 大小: '.$uploadSetting['file_types']['video']['upload_max_filesize'].' 扩展名: '.$uploadSetting['file_types']['video']['extensions'].'  ';
			}
			
			
			if($uploadSetting['file_types']['audio']['upload_max_filesize'] !=$olduploadSetting['file_types']['audio']['upload_max_filesize'] || $uploadSetting['file_types']['audio']['extensions'] !=$olduploadSetting['file_types']['audio']['extensions']){
				$action.='音频文件 大小: '.$uploadSetting['file_types']['audio']['upload_max_filesize'].' 扩展名: '.$uploadSetting['file_types']['audio']['extensions'].'  ';
			}
			
			if($uploadSetting['file_types']['file']['upload_max_filesize'] !=$olduploadSetting['file_types']['file']['upload_max_filesize'] || $uploadSetting['file_types']['file']['extensions'] !=$olduploadSetting['file_types']['file']['extensions']){
				$action.='附件 大小: '.$uploadSetting['file_types']['file']['upload_max_filesize'].' 扩展名: '.$uploadSetting['file_types']['file']['extensions'].'  ';
			}
			
			
			if($action!="修改上传设置 "){
				setAdminLog($action);
			}
			
			
			

           
            $this->success('保存成功！');
        }

    }

    /**
     * 清除缓存
     * @adminMenu(
     *     'name'   => '清除缓存',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '清除缓存',
     *     'param'  => ''
     * )
     */
    public function clearCache()
    {
        $content = hook_one('admin_setting_clear_cache_view');

        if (!empty($content)) {
            return $content;
        }

        cmf_clear_cache();
        return $this->fetch();
    }
    
    /**
     * 私密设置
     */
    public function configpri(){
        $siteinfo=cmf_get_option('site_info');
        $name_coin=$siteinfo['name_coin'];
        $this->assign('name_coin',$name_coin);
        $this->assign('config', cmf_get_option('configpri'));

        return $this->fetch();
    }

    /**
     * 私密设置提交
     */
    public function configpriPost(){

        if ($this->request->isPost()) {
			
			
			$oldconfigpri=cmf_get_option('configpri');
            
            $options = $this->request->param('options/a');

            if($options['reg_reward']==''){
                $this->error("登录配置请填写注册奖励");
            }

            if(!is_numeric($options['reg_reward'])){
                $this->error("注册奖励必须为数字");
            }

            if(floor($options['reg_reward']) !=$options['reg_reward']){
                $this->error("注册奖励必须为整数");  
            }

            if($options['iplimit_times']==''){
                $this->error("登录配置请填写短信验证码IP限制次数");
            }

            if(!is_numeric($options['iplimit_times'])){
                $this->error("短信验证码IP限制次数必须为数字");
            }

            if(floor($options['iplimit_times']) !=$options['iplimit_times']){
                $this->error("短信验证码IP限制次数必须为整数");  
            }

            if($options['level_limit']==''){
                $this->error("直播配置请填写直播限制等级");
            }

            if(!is_numeric($options['level_limit'])){
                $this->error("直播限制等级必须为数字");
            }

            if(floor($options['level_limit']) !=$options['level_limit']){
                $this->error("直播限制等级必须为整数");  
            }

            if($options['speak_limit']==''){
                $this->error("直播配置请填写发言等级限制");
            }

            if(!is_numeric($options['speak_limit'])){
                $this->error("发言等级限制必须为数字");
            }

            if(floor($options['speak_limit']) !=$options['speak_limit']){
                $this->error("发言等级限制必须为整数");  
            }

            if($options['barrage_limit']==''){
                $this->error("直播配置请填写弹幕等级限制");
            }

            if(!is_numeric($options['barrage_limit'])){
                $this->error("弹幕等级限制必须为数字");
            }

            if(floor($options['barrage_limit']) !=$options['barrage_limit']){
                $this->error("弹幕等级限制必须为整数");  
            }

            if($options['barrage_fee']==''){
                $this->error("直播配置请填写弹幕费用");
            }

            if(!is_numeric($options['barrage_fee'])){
                $this->error("弹幕费用必须为数字");
            }

            if(floor($options['barrage_fee']) !=$options['barrage_fee']){
                $this->error("弹幕费用必须为整数");  
            }


            if($options['distribut1']>40){
                $this->error("邀请一级分成不能大于40%！");
            }

            if($options['userlist_time']==''){
                $this->error("直播配置请填写用户列表请求间隔");
            }

            if(!is_numeric($options['userlist_time'])){
                $this->error("用户列表请求间隔必须为数字");
            }

            if(floor($options['userlist_time']) !=$options['userlist_time']){
                $this->error("用户列表请求间隔必须为整数");  
            }
            
            if($options['userlist_time']<5){
                $this->error("用户列表请求间隔不能小于5秒");
            }

            if($options['mic_limit']==''){
                $this->error("直播配置请填写连麦等级限制");
            }

            if(!is_numeric($options['mic_limit'])){
                $this->error("连麦等级限制必须为数字");
            }

            if(floor($options['mic_limit']) !=$options['mic_limit']){
                $this->error("连麦等级限制必须为整数");  
            }

            
            
            $game_switch=isset($_POST['game_switch'])?$_POST['game_switch']:'';
            
            $options['game_switch']='';
            
            if($game_switch){
                $options['game_switch']=implode(',',$game_switch);
            }

//            $shop_payment_time=$options['shop_payment_time'];
//
//            if($shop_payment_time<1){
//                $this->error("店铺付款失效时间必须大于0");
//            }
//
//            if(floor($shop_payment_time)!=$shop_payment_time){
//                $this->error("店铺付款失效时间必须为正整数");
//            }
//
//            $shop_shipment_time=$options['shop_shipment_time'];
//
//            if($shop_shipment_time<1){
//                $this->error("店铺发货失效时间必须大于0");
//            }
//
//            if(floor($shop_shipment_time)!=$shop_shipment_time){
//                $this->error("店铺发货失效时间必须为正整数");
//            }
//
//            $shop_receive_time=$options['shop_receive_time'];
//
//            if($shop_receive_time<1){
//                $this->error("店铺自动确认收货时间必须大于0");
//            }
//
//            if(floor($shop_receive_time)!=$shop_receive_time){
//                $this->error("店铺自动确认收货时间必须为正整数");
//            }
//
//
//            $shop_refund_time=$options['shop_refund_time'];
//
//            if($shop_refund_time<1){
//                $this->error("买家发起退款,卖家不做处理自动退款时间必须大于0");
//            }
//
//            if(floor($shop_refund_time)!=$shop_refund_time){
//                $this->error("买家发起退款,卖家不做处理自动退款时间必须为正整数");
//            }
//
//
//            $shop_refund_finish_time=$options['shop_refund_finish_time'];
//
//            if($shop_refund_finish_time<1){
//                $this->error("卖家拒绝买家退款后,买家不做任何操作,退款自动完成时间必须大于0");
//            }
//
//            if(floor($shop_refund_finish_time)!=$shop_refund_finish_time){
//                $this->error("卖家拒绝买家退款后,买家不做任何操作,退款自动完成时间必须为正整数");
//            }
			
			$options['sensitive_words']=str_replace("+","",$options['sensitive_words']);

            cmf_set_option('configpri', $options,true);
            $this->resetcache('getConfigPri',$options);

            setcaches('sensitive_words',explode(',',$options['sensitive_words']));
			
			
			$action="修私密配置 ";
            if($options['family_switch'] !=$oldconfigpri['family_switch']){
                $family_switch=$options['family_switch']?'开':'关';
                $action.='家族控制开关 '.$family_switch.' ';
            }
			
			if($options['family_member_divide_switch'] !=$oldconfigpri['family_member_divide_switch']){
                $family_member_divide_switch=$options['family_member_divide_switch']?'开':'关';
                $action.='家族长修改成员分成比例是否管理员审核 '.$family_member_divide_switch.' ';
            }
			
			if($options['service_switch'] !=$oldconfigpri['service_switch']){
                $service_switch=$options['service_switch']?'开':'关';
                $action.='客服 '.$service_switch.' ';
            }
			
			if($options['service_url'] !=$oldconfigpri['service_url']){
                $action.='客服链接 ';
            }
			
			if($options['sensitive_words'] !=$oldconfigpri['sensitive_words']){
                $action.='敏感词 ';
            }
			
			
			if($options['reg_reward'] !=$oldconfigpri['reg_reward']){
                $action.='注册奖励 '.$options['reg_reward'].' ';
            }
			
			if($options['bonus_switch'] !=$oldconfigpri['bonus_switch']){
                $bonus_switch=$options['bonus_switch']?'开':'关';
                $action.='登录奖励开关 '.$bonus_switch.' ';
            }
			
			if($options['sendcode_switch'] !=$oldconfigpri['sendcode_switch']){
                $sendcode_switch=$options['sendcode_switch']?'开':'关';
                $action.='短信验证码开关 '.$sendcode_switch.' ';
            }
			
//			if($options['typecode_switch'] !=$oldconfigpri['typecode_switch']){
//                $typecode_switch=$options['typecode_switch']==1?'阿里云':'容联云';
//                $action.='短信接口平台 '.$typecode_switch.' ';
//            }
			
			if($options['iplimit_switch'] !=$oldconfigpri['iplimit_switch']){
                $iplimit_switch=$options['iplimit_switch']?'开':'关';
                $action.='短信验证码IP限制开关 '.$iplimit_switch.' ';
            }
			
			if($options['iplimit_times'] !=$oldconfigpri['iplimit_times']){
                $action.='短信验证码IP限制次数 '.$options['iplimit_times'].' ';
            }
			
			if($options['auth_islimit'] !=$oldconfigpri['auth_islimit']){
                $auth_islimit=$options['auth_islimit']?'开':'关';
                $action.='认证限制 '.$auth_islimit.' ';
            }
			
			if($options['level_islimit'] !=$oldconfigpri['level_islimit']){
                $level_islimit=$options['level_islimit']?'开':'关';
                $action.='直播等级控制 '.$level_islimit.' ';
            }
			
			if($options['level_limit'] !=$oldconfigpri['level_limit']){
                $action.='直播限制等级 '.$options['level_limit'].' ';
            }
			
			if($options['speak_limit'] !=$oldconfigpri['speak_limit']){
                $action.='发言等级限制 '.$options['speak_limit'].' ';
            }
			
			
			if($options['barrage_limit'] !=$oldconfigpri['barrage_limit']){
                $action.='弹幕等级限制 '.$options['barrage_limit'].' ';
            }
			
			if($options['barrage_fee'] !=$oldconfigpri['barrage_fee']){
                $action.='弹幕费用 '.$options['barrage_fee'].' ';
            }
			
			if($options['userlist_time'] !=$oldconfigpri['userlist_time']){
                $action.='用户列表请求间隔(秒) '.$options['userlist_time'].' ';
            }
			
			if($options['mic_limit'] !=$oldconfigpri['mic_limit']){
                $action.='连麦等级限制 '.$options['mic_limit'].' ';
            }
			
			if($options['chatserver'] !=$oldconfigpri['chatserver']){
                $action.='聊天服务器带端口 '.$options['chatserver'].' ';
            }
			
			if($options['live_sdk'] !=$oldconfigpri['live_sdk']){
				$live_sdk=$options['live_sdk']?'直播+连麦模式':'直播模式';
                $action.='直播模式选择 '.$live_sdk.' ';
            }

            if($options['live_notice'] !=$oldconfigpri['live_notice']){
                $action.='直播公告 '.$options['live_notice'].' ';
            }

            if($options['lbcz_setup'] !=$oldconfigpri['lbcz_setup']){
                $action.='录播彩种设置 '.$options['lbcz_setup'].' ';
            }
            
            if($options['card_money'] !=$oldconfigpri['card_money']){
                $action.='录播名片查看金额 '.$options['card_money'].' ';
            }

            
            if($options['card'] !=$oldconfigpri['card']){
                $action.='录播微信名片 '.$options['card'].' ';
            }
			
// 			if($options['cdn_switch'] !=$oldconfigpri['cdn_switch']){
// 				$live_sdk=[
// 					'1'=>'阿里云',
// 					'2'=>'腾讯云',
// 					'3'=>'七牛云',
// 					'4'=>'网宿',
// 					'5'=>'网易云',
// 					'6'=>'奥点云',
				
// 				];
//                 $action.='直播CDN '.$live_sdk[$options['cdn_switch']].' ';
//             }
			
			
// 			if($options['tx_play_key_switch'] !=$oldconfigpri['tx_play_key_switch']){
//                 $tx_play_key_switch=$options['tx_play_key_switch']?'开':'关';
//                 $action.='是否开启腾讯云播流鉴权 '.$tx_play_key_switch.' ';
//             }
			
			
// 			if($options['tx_push'] !=$oldconfigpri['tx_push']){
//                 $action.='腾讯云直播推流域名 '.$options['tx_push'].' ';
//             }
			
// 			if($options['tx_pull'] !=$oldconfigpri['tx_pull']){
//                 $action.='腾讯云直播播流域名 '.$options['tx_pull'].' ';
//             }
			
//            if($options['cash_rate']<=0 || !is_numeric($options['cash_rate']) || floor($options['cash_rate'])!=$options['cash_rate']){
//                $this->error("映票提现比例必须为正整数");
//            }
			
//			if($options['cash_rate'] !=$oldconfigpri['cash_rate']){
//                $action.='提现比例 '.$options['cash_rate'].' ';
//            }

//            if($options['cash_take']<0 || !is_numeric($options['cash_take']) || floor($options['cash_take'])!=$options['cash_take']){
//                $this->error("映票提现抽成必须为大于等于0的整数");
//            }
//
//			if($options['cash_take'] !=$oldconfigpri['cash_take']){
//                $action.='提现抽成'.$options['cash_take'].'(元) ';
//            }
			
			if($options['cash_min'] !=$oldconfigpri['cash_min']){
                $action.='提现最低额度'.$options['cash_min'].'（元） ';
            }
			
			if($options['cash_start'] !=$oldconfigpri['cash_start'] || $options['cash_end'] !=$oldconfigpri['cash_end']){
                $action.='每月提现期 '.$options['cash_start'].'-'.$options['cash_end'].' ';
            }
			
			if($options['cash_max_times'] !=$oldconfigpri['cash_max_times']){
                $action.='每月提现次数'.$options['cash_max_times'].' ';
            }
			
			if($options['letter_switch'] !=$oldconfigpri['letter_switch']){
                $letter_switch=$options['letter_switch']?'开':'关';
                $action.='私信开关 '.$letter_switch.' ';
            }
			
			
			if($options['jpush_sandbox'] !=$oldconfigpri['jpush_sandbox']){
                $jpush_sandbox=$options['jpush_sandbox']?'生产':'开发';
                $action.='极光推送模式 '.$jpush_sandbox.' ';
            }
			
			
			
//			if($options['aliapp_switch'] !=$oldconfigpri['aliapp_switch']){
//                $aliapp_switch=$options['aliapp_switch']?'开':'关';
//                $action.='支付宝APP开关 '.$aliapp_switch.' ';
//            }
//
//
//			if($options['wx_switch'] !=$oldconfigpri['wx_switch']){
//                $wx_switch=$options['wx_switch']?'开':'关';
//                $action.='微信APP开关 '.$wx_switch.' ';
//            }
//
//
//			if($options['shop_aliapp_switch'] !=$oldconfigpri['shop_aliapp_switch']){
//                $shop_aliapp_switch=$options['shop_aliapp_switch']?'开':'关';
//                $action.='店铺支付宝支付APP开关 '.$shop_aliapp_switch.' ';
//            }
//
//
//			if($options['shop_wx_switch'] !=$oldconfigpri['shop_wx_switch']){
//                $shop_wx_switch=$options['shop_wx_switch']?'开':'关';
//                $action.='店铺微信支付APP开关 '.$shop_wx_switch.' ';
//            }
//
//			if($options['shop_balance_switch'] !=$oldconfigpri['shop_balance_switch']){
//                $shop_balance_switch=$options['shop_balance_switch']?'开':'关';
//                $action.='店铺余额支付APP开关 '.$shop_balance_switch.' ';
//            }
//
//			if($options['paidprogram_aliapp_switch'] !=$oldconfigpri['paidprogram_aliapp_switch']){
//                $paidprogram_aliapp_switch=$options['paidprogram_aliapp_switch']?'开':'关';
//                $action.='付费内容支付宝支付APP开关 '.$paidprogram_aliapp_switch.' ';
//            }
//
//			if($options['paidprogram_wx_switch'] !=$oldconfigpri['paidprogram_wx_switch']){
//                $paidprogram_wx_switch=$options['paidprogram_wx_switch']?'开':'关';
//                $action.='付费内容微信支付APP开关 '.$paidprogram_wx_switch.' ';
//            }
//
//			if($options['paidprogram_balance_switch'] !=$oldconfigpri['paidprogram_balance_switch']){
//                $paidprogram_balance_switch=$options['paidprogram_balance_switch']?'开':'关';
//                $action.='付费内容余额支付APP开关 '.$paidprogram_balance_switch.' ';
//            }
			
			if($options['agent_switch'] !=$oldconfigpri['agent_switch']){
                $agent_switch=$options['agent_switch']?'开':'关';
                $action.='邀请开关 '.$agent_switch.' ';
            }
			
			if($options['distribut1'] !=$oldconfigpri['distribut1']){
                $action.='一级分成 '.$options['distribut1'].' ';
            }

            if($options['reward'] !=$oldconfigpri['reward']){
                $action.='邀请奖励(钱) '.$options['reward'].' ';
            }

            if($options['look_video'] !=$oldconfigpri['look_video']){
                $action.='邀请奖励(视频) '.$options['look_video'].' ';
            }

            if($options['tripartite_agent'] !=$oldconfigpri['tripartite_agent']){
                $action.='三方游戏域名 '.$options['tripartite_agent'].' ';
            }

            if($options['tripartite_domain'] !=$oldconfigpri['tripartite_domain']){
                $action.='三方游戏代理账号 '.$options['tripartite_domain'].' ';
            }

            if($options['tripartite_key'] !=$oldconfigpri['tripartite_key']){
                $action.='三方游戏密钥 '.$options['tripartite_key'].' ';
            }

            if($options['tripartite_game_key'] !=$oldconfigpri['tripartite_game_key']){
                $action.='三方彩票密钥 '.$options['tripartite_game_key'].' ';
            }
            if($options['tripartite_game_url'] !=$oldconfigpri['tripartite_game_url']){
                $action.='三方彩票地址 '.$options['tripartite_game_url'].' ';
            }

            if($options['bai_du_url'] !=$oldconfigpri['bai_du_url']){
                $action.='百度翻译地址 '.$options['bai_du_url'].' ';
            }

            if($options['bai_du_app_id'] !=$oldconfigpri['bai_du_app_id']){
                $action.='百度翻译APPID '.$options['bai_du_app_id'].' ';
            }

            if($options['bai_du_sec_key'] !=$oldconfigpri['bai_du_sec_key']){
                $action.='百度翻译密钥 '.$options['bai_du_sec_key'].' ';
            }

            if($options['qiniu_accesskey'] !=$oldconfigpri['qiniu_accesskey']){
                $action.='accessKey '.$options['qiniu_accesskey'].' ';
            }

            if($options['qiniu_secretkey'] !=$oldconfigpri['qiniu_secretkey']){
                $action.='secretKey '.$options['qiniu_secretkey'].' ';
            }

            if($options['qiniu_space_bucket'] !=$oldconfigpri['qiniu_space_bucket']){
                $action.='存储空间 '.$options['qiniu_space_bucket'].' ';
            }

            if($options['qiniu_space_host'] !=$oldconfigpri['qiniu_space_host']){
                $action.='cdn加速域名 '.$options['qiniu_space_host'].' ';
            }

            if($options['qiniu_uphost'] !=$oldconfigpri['qiniu_uphost']){
                $action.='区域上传域名 '.$options['qiniu_uphost'].' ';
            }

            if($options['qiniu_region'] !=$oldconfigpri['qiniu_region']){
                $action.='七牛云存储区域 '.$options['qiniu_region'].' ';
            }

            if($options['withdrawal_procedures'] !=$oldconfigpri['withdrawal_procedures']){
                $action.='提现手续费 '.$options['withdrawal_procedures'].' ';
            }
			
			
			if($options['video_audit_switch'] !=$oldconfigpri['video_audit_switch']){
                $video_audit_switch=$options['video_audit_switch']?'开':'关';
                $action.='视频审核开关 '.$video_audit_switch.' ';
            }

            if($options['free_look_nums'] !=$oldconfigpri['free_look_nums']){
                $action.='每日免费观影次数 '.$options['free_look_nums'].' ';
            }
			
//			if($options['shop_system_name'] !=$oldconfigpri['shop_system_name']){
//                $action.='系统店铺名称 '.$options['shop_system_name'].' ';
//            }
//
//			if($options['shop_bond'] !=$oldconfigpri['shop_bond']){
//                $action.='申请店铺需要的保证金 '.$options['shop_bond'].' ';
//            }
//
//			if($options['show_switch'] !=$oldconfigpri['show_switch']){
//                $show_switch=$options['show_switch']?'开':'关';
//                $action.='店铺审核 '.$show_switch.' ';
//            }
//
//			if($options['shoporder_percent'] !=$oldconfigpri['shoporder_percent']){
//                $action.='店铺订单默认抽成比例 '.$options['shoporder_percent'].' ';
//            }
//
//			if($options['goods_switch'] !=$oldconfigpri['goods_switch']){
//                $goods_switch=$options['goods_switch']?'开':'关';
//                $action.='商品审核 '.$goods_switch.' ';
//            }
//
//			if($options['shop_certificate_desc'] !=$oldconfigpri['shop_certificate_desc']){
//                $action.='店铺资质说明 '.$options['shop_certificate_desc'].' ';
//            }
//
//			if($options['shop_payment_time'] !=$oldconfigpri['shop_payment_time']){
//                $action.='店铺付款失效时间(分钟) '.$options['shop_payment_time'].' ';
//            }
//
//			if($options['shop_shipment_time'] !=$oldconfigpri['shop_shipment_time']){
//                $action.='店铺发货失效时间(天) '.$options['shop_shipment_time'].' ';
//            }
//
//			if($options['shop_receive_time'] !=$oldconfigpri['shop_receive_time']){
//                $action.='店铺自动确认收货时间(天) '.$options['shop_receive_time'].' ';
//            }
//
//			if($options['shop_refund_time'] !=$oldconfigpri['shop_refund_time']){
//                $action.='买家发起退款,卖家不做处理自动退款时间(天) '.$options['shop_refund_time'].' ';
//            }
//
//			if($options['shop_refund_finish_time'] !=$oldconfigpri['shop_refund_finish_time']){
//                $action.='卖家拒绝买家退款后,买家不做任何操作,订单自动进入退款前状态的时间(天)< '.$options['shop_refund_finish_time'].' ';
//            }
//
//			if($options['shop_receive_refund_time'] !=$oldconfigpri['shop_receive_refund_time']){
//                $action.='订单确认收货后,支持退货退款的时间限制(天) '.$options['shop_receive_refund_time'].' ';
//            }
//
//			if($options['shop_settlement_time'] !=$oldconfigpri['shop_settlement_time']){
//                $action.='订单确认收货后,货款自动打到卖家的时间(天) '.$options['shop_settlement_time'].' ';
//            }
//
//			if($options['balance_cash_min'] !=$oldconfigpri['balance_cash_min']){
//                $action.='余额提现最低额度（元） '.$options['balance_cash_min'].' ';
//            }
//
//			if($options['balance_cash_start'] !=$oldconfigpri['balance_cash_start'] || $options['balance_cash_end'] !=$oldconfigpri['balance_cash_end']){
//                $action.='余额每月提现期限 '.$options['balance_cash_start'].'-'.$options['balance_cash_end'].' ';
//            }
//
//			if($options['balance_cash_max_times'] !=$oldconfigpri['balance_cash_max_times']){
//                $action.='每月提现次数 '.$options['balance_cash_max_times'].' ';
//            }
			
			if($options['dynamic_auth'] !=$oldconfigpri['dynamic_auth']){
                $dynamic_auth=$options['dynamic_auth']?'开':'关';
                $action.='动态认证开关 '.$dynamic_auth.' ';
            }
			
			if($options['dynamic_switch'] !=$oldconfigpri['dynamic_switch']){
                $dynamic_switch=$options['dynamic_switch']?'开':'关';
                $action.='动态审核 '.$dynamic_switch.' ';
            }
			
			if($options['comment_weight'] !=$oldconfigpri['comment_weight']){
                $action.='评论权重值 '.$options['comment_weight'].' ';
            }
			
			
			if($options['like_weight'] !=$oldconfigpri['like_weight']){
                $action.='点赞权重值 '.$options['like_weight'].' ';
            }
			
			

			if($options['game_switch'] !=$oldconfigpri['game_switch']){
                $action.='游戏开关 '.$options['game_switch'].' ';
            }
			
			
			if($options['game_banker_limit'] !=$oldconfigpri['game_banker_limit']){
                $action.='上庄限制 '.$options['game_banker_limit'].' ';
            }
			
			if($options['game_odds'] !=$oldconfigpri['game_odds']){
                $action.='普通游戏赔率 '.$options['game_odds'].' ';
            }
			
			if($options['game_odds_p'] !=$oldconfigpri['game_odds_p']){
                $action.='系统坐庄游戏赔率 '.$options['game_odds_p'].' ';
            }
			
			if($options['game_odds_u'] !=$oldconfigpri['game_odds_u']){
                $action.='用户坐庄游戏赔率 '.$options['game_odds_u'].' ';
            }
			
			if($options['game_pump'] !=$oldconfigpri['game_pump']){
                $action.='游戏抽水 '.$options['game_pump'].' ';
            }
			
			
			if($options['turntable_switch'] !=$oldconfigpri['turntable_switch']){
                $turntable_switch=$options['turntable_switch']?'开':'关';
                $action.='直播间大转盘开关 '.$turntable_switch.' ';
            }
			
//			if($options['express_type'] !=$oldconfigpri['express_type']){
//                $express_type=$options['express_type']?'正式版':'开发版';
//                $action.='物流模式 '.$express_type.' ';
//            }
			
			if($options['watch_live_term'] !=$oldconfigpri['watch_live_term'] || $options['watch_live_coin'] !=$oldconfigpri['watch_live_coin']){
           
                $action.='观看直播 条件(分钟)：'.$options['watch_live_term'].'奖励(钻石)：'.$options['watch_live_coin'].' ';
            }
            
			
			if($options['watch_video_term'] !=$oldconfigpri['watch_video_term'] || $options['watch_video_coin'] !=$oldconfigpri['watch_video_coin']){
           
                $action.='观看视频 条件(分钟)：'.$options['watch_video_term'].'奖励(钻石)：'.$options['watch_video_coin'].' ';
            }
			
			if($options['open_live_term'] !=$oldconfigpri['open_live_term'] || $options['open_live_coin'] !=$oldconfigpri['open_live_coin']){
           
                $action.='直播奖励 条件(分钟)：'.$options['open_live_term'].'奖励(钻石)：'.$options['open_live_coin'].' ';
            }
			
			
			if($options['award_live_term'] !=$oldconfigpri['award_live_term'] || $options['award_live_coin'] !=$oldconfigpri['award_live_coin']){
           
                $action.='打赏奖励 条件(分钟)：'.$options['award_live_term'].'奖励(钻石)：'.$options['award_live_coin'].' ';
            }
			
			if($options['share_live_term'] !=$oldconfigpri['share_live_term'] || $options['share_live_coin'] !=$oldconfigpri['share_live_coin']){
           
                $action.='分享奖励 条件(分钟)：'.$options['share_live_term'].'奖励(钻石)：'.$options['share_live_coin'].' ';
            }
			
            if($action!="修私密配置 "){
				setAdminLog($action);
			}

            $this->success("保存成功！", '');

        }
    }

    protected function resetcache($key='',$info=[]){
        if($key!='' && $info){
            delcache($key);
            setcaches($key,$info);
        }
    }

}