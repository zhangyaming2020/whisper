<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;
/**
 * redis连接类
 * @author zhangyaming
 *
 */
class RedisConnector
{
    public static function connRedis($name)
    {
        $redis_config = Config::get($name);
        if (empty($redis_config)) {
            return false;
        }

        $redis=new \Redis();
        $redis->connect($redis_config['host'],$redis_config['port']);
        return $redis;
    }
}
