# 小说网站  使用Laravel开发

## gitignore 文件存放的是git忽略文件

# 需要手动生成目录
    storage
        app
            public
        framework
            cache
            sessions
            views
        logs  
# 需要手动生成配置文件 .env
    PP_ENV=local
    APP_DEBUG=true
    APP_KEY=
    APP_URL=http://www.jq-o.com

    DB_HOST=127.0.0.1
    DB_DATABASE=forge
    DB_USERNAME=forge
    DB_PASSWORD=

    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=sync

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    MAIL_DRIVER=smtp
    MAIL_HOST=mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null  
