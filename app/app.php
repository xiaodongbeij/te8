<?php

return [

        // 异常页面的模板文件
   //'exception_tmpl'         => CMF_ROOT . '/themes/404.html',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认语言
    'default_lang'           => 'zh-cn',
    
    // 默认模块名
    'default_module'         => 'Portal',
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',

    //ticket_key
    'ticket_key'    =>  'uHai9bCz',
    //三方游戏
    'game' => [
        'domain' => 'http://net4.testqu.com',
        'agent' => 'c08_btx',
        'key' => 'TWBLFK6KZ5',
    ],
    
    'telegram' => 'https://api.telegram.org/bot1716954932:AAFFi_raD8um5WWp3HsDcWpBDwa1DUSK80w/sendMessage?chat_id=-433848225&text=',

    //返点平台
    'rate_plat' => [
        ['platform'=>"1",'remark'=>'彩票'],
        ['platform'=>"2",'remark'=>'直播'],
        ['platform'=>"0016",'remark'=>'开元棋牌'],
        ['platform'=>"0004",'remark'=>'AG游戏'],
        ['platform'=>"0027",'remark'=>'OG游戏'],
        ['platform'=>"0022",'remark'=>'德胜棋牌'],
        ['platform'=>"0002",'remark'=>'PT游戏'],
        ['platform'=>"0024",'remark'=>'速博体育'],
        ['platform'=>"0035",'remark'=>'泛亚电竞'],
    ]
];


