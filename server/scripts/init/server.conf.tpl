server
{
    listen       80;
    server_name DOMAIN_NAME;
    index index.html index.htm index.php;
    root  ROOT_DIR;

    charset utf-8;
     
    expires  off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~*  .*\.(rar|zip|tar|tar.gz|tar.bz2|swp|svn|subversion)$
    {
            return 404;
    }
    location ~* .swp$ 
    { 
            return 404; 
    }
    location ~* .svn$
    {
            return 404;
    }

    location ~ .*\.swf$ {
             expires       72h;
    }
    location ~ .*\.css$ {
             expires       96h;
    }
    location ~ .*\.xml$ {
             expires       12h;
    }
    location ~ .*\.js$ {
             expires       96h;
    }
    location ~ .*\.jpg$ {
             expires       96h;
    }
    location ~ .*\.gif$ {
             expires       96h;
    }
    location ~ .*\.png$ {
             expires       96h;
    }
    location ~ .*\.mp3$ {
             expires       400h;
    }


    access_log off;


    location ~ .*\.php$
    {
            proxy_set_header   Host             $host;
            proxy_set_header   X-Real-IP        $remote_addr;
            proxy_set_header   X-Forwarded-For  $proxy_add_x_forwarded_for;

            proxy_connect_timeout 10;
            proxy_send_timeout 5;
            proxy_read_timeout 8;
            proxy_buffer_size 4k;
            proxy_buffers 4 32k;
            proxy_busy_buffers_size 64k;
            proxy_temp_file_write_size 64k;
            proxy_temp_path  /dev/shm;

            include fastcgi.conf;
            fastcgi_pass  127.0.0.1:10080;
            fastcgi_index index.php;

            access_log  /data/logs/mingchao_php_only.log  access buffer=32k;
    }
}
