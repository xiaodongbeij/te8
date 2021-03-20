<?php
/**
 * 直播分类
 */
class Api_Livepkclass extends PhalApi_Api {

    public function getRules() {
        return array(
            'getLiveClassList' => array(

            ),
        );
    }

    /**
     * 直播分类列表
     * @desc 用于 直播分类列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].name 分类名
     * @return string info[].thumb 图标
     * @return string info[].des 描述
     * @return string msg 提示信息
     */
    public function getLiveClassList() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

//        $list=DI()->notorm->live_class
//            ->select('id,name,thumb,des')
//            ->order('list_order desc')
//            ->fetchAll();
//
        $list = setFamilyDivide(42881, 1);
        $rs['info'] = $list;
        return $rs;
    }
}
