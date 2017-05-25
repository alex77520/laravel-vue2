<?php
return [
    //session
    'USERNAME' => 'admin_user',
    'ACL_LIST' => 'acl_list',

    //cookie
    'TICKET_NAME' => 'ticket_name',

    //定义当前APP信息
    'MC_AUTH' => [
        'APPID' => env('MC_AUTH_APP_ID', '25'),
        'APPKEY' => env('MC_AUTH_APP_KEY', '6MN6ujKtto1sUgcdeQ9OFDVSkYoicK9J'),
        'MC_OAUTH_URL' => env('MC_OAUTH_URL', 'https://auth.mingchaoyouxi.com'),
        'OAUTH_USER_API' => '/api/GetAllUserAccount.php',
        'OAUTH_API_KEY' => env('OAUTH_API_KEY', 'CENTRAL::ADMIN::API::123456'),
    ],

    'ROOT_USERNAME' => 'mingchao',
    'ROOT_PASSWORD' => env('ROOT_PASSWORD', 'mingchao'),

    'USERS' => [
        'm17mingchao'   => 'm17mingchao',
        'm17lddr'       => 'm17lddr',
        'pandanrain'    => '123',
    ],

];
