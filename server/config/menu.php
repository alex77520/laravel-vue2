<?php
/**
 * 权限项配置
 */

return [
    // 'menuList' => [
    //     ['auth' => 'logApp', 'path'=>'/logApp', 'menu' => '项目配置'],
    //     ['auth' => 'logType', 'path'=>'/logType', 'menu' => '日志类型'],
    //     ['auth' => 'logTable', 'path'=>'/logTable', 'menu' => '表结构配置'],
    // ],
    // 'aclList' => [
    //     'mdbConf' => [
    //         'name' => '大数据配置系统',
    //         'modules' => [
    //             'logApp' => [
    //                 'name' => '项目配置',
    //                 'actions' => [
    //                     'index' => [
    //                         'name' => '项目配置查看'
    //                     ]
    //                 ]
    //             ],
    //             'logType' => [
    //                 'name' => '日志类型',
    //                 'actions' => [
    //                     'index' => [
    //                         'name' => '日志类型查看'
    //                     ]
    //                 ]
    //             ],
    //             'logTable' => [
    //                 'name' => '表结构配置',
    //                 'actions' => [
    //                     'index' => [
    //                         'name' => '表结构查看'
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ]

    'menuList' => [
        ['auth' => 1, 'path' => '/report', 'menu' => '举报列表'],
        ['auth' => 1, 'path' => '/feedback', 'menu' => '反馈列表'],
        ['auth' => 1, 'path' => '/topicCategory', 'menu' => '话题分类'],
        ['auth' => 1, 'path' => '/topicCreate', 'menu' => '创建话题'],
        ['auth' => 1, 'path' => '/topicList', 'menu' => '话题列表'],
        ['auth' => 1, 'path' => '/libraryList', 'menu' => '题库列表'],
        ['auth' => 1, 'path' => '/questionReportList', 'menu' => '错题举报列表'],
        ['auth' => 1, 'path' => '/recommendList', 'menu' => '热议配置'],
        ['auth' => 1, 'path' => '/reviewList', 'menu' => '内容审核'],
    ]
];