<?php
namespace app\index\controller;

use think\Config;
use think\Controller;

use think\RedisConnector;
use think\common\ShowRedisKey;
use think\Cache;
use think\Db;
class Index extends Controller
{
    public function _initialize()
    {
        $config = Config::get('db');
        $this->db = Db::connect($config);
    }
    public function index()
    {
        /*
        //H+后台主题模板：https://demo.demohuo.top/modals/40/4078/demo/
        $redis = RedisConnector::connRedis('whisper');
        $json =json_encode(array(
            'name'=>'规格化',
            'age'=>2
        ));
// This first case: hash 值不存在
        $redis -> hSet('myhash','favorite_fruit',$json);
        var_dump($redis -> hGet('myhash','favorite_fruit'));    // string
        exit;//*/
        return $this->fetch();
    }

    // pc客户端
    public function chat()
    {
        // 跳转到移动端
        if(request()->isMobile()){
            $param = http_build_query([
                'id' => input('param.id'),
                'name' => input('param.name'),
                'group' => input('param.group'),
                'avatar' => input('param.avatar')
            ]);
            $this->redirect('/index/index/mobile?' . $param);
        }

        $this->assign([
            'socket' => config('socket'),
            'id' => input('param.id'),
            'name' => input('param.name'),
            'group' => input('param.group'),
            'avatar' => input('param.avatar'),
        ]);

        return $this->fetch();
    }

    // 移动客户端
    public function mobile()
    {
        $this->assign([
            'socket' => config('socket'),
            'id' => input('param.id'),
            'name' => input('param.name'),
            'group' => input('param.group'),
            'avatar' => input('param.avatar'),
        ]);

        return $this->fetch();
    }
    //支付回调
    public function wxcallback(){
        /*
        $testxml  = file_get_contents("php://input");
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $result = json_decode($jsonxml, true);//转成数组，
        writeLog('pay_notify','回调数据'.$jsonxml);exit;
        if($result){
            //如果成功返回了
            $out_trade_no = $result['out_trade_no'];
            /*
            if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                　　

            }
        }
        */
        $config = Config::get('db');
        $obj = $this->db; $res = $obj->name('order')->where('status',1)->select();
        dump($res);exit;
    }
}
