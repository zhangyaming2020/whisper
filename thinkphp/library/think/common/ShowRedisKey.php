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

namespace think\common;

/**
 *  redis key汇总类
 * @author huqq
 * @
 */
class ShowRedisKey
{
    /**
     *  $params为参数数组,如$params = array('key', 'key2');
     *  以carInList为例：
     *      ShowRedisKey::carInList(array('a', 'b'));
     *  返回结果为：
     *      car_in_list_a_b
     */


    /**
     * 车辆进场队列
     * @param array $params
     * @return string
     */
    public static function carInList($params = array())
    {
        $key = 'car_in_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /***
     * 车辆出场队列
     * @param array $params
     * @return string
     */
    public static function carOutList($params = array())
    {
        $key = 'car_out_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 三方消息推送notify
     * @param array $params
     * @return string
     */
    public static function thirdNotifyExpire($params = array())
    {
        $key = 'third_notify_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * 三方消息推送notify
     * @param array $params
     * @return string
     */
    public static function thirdNotifyDelayExpire($params = array())
    {
        $key = 'third_notify_delay_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * 停车场剩余车位
     * @param array $params
     * @return string
     */
    public static function lastParkSpace($params = array())
    {
        $key = 'last_park_space_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 模块化计费按固定天数收取首停的日期
     * @param array $params
     * @return string
     */
    public static function firstStopDate($params = array())
    {
        $key = 'first_stop_date_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }


    /**
     * 车辆进场信息
     * @param array $params
     * @return string
     */
    public static function carInInfo($params = array())
    {
        $key = 'car_in_info';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }


    /**
     * 用于收集用户的行为日志
     * @param array $params
     * @return string
     */
    public static function logCenterActionLogList($params = array())
    {
        $key = 'action_log_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 用于收集平台最新的top行为记录
     * @param array $params
     * @return string
     */
    public static function logCenterNewTopActionLogList($params = array())
    {
        $key = 'new_top_action_log_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }


    /**
     * 用于收集单个服务器的nginx日志
     * @param array $params
     * @return string
     */
    public static function logCenterNginxAccessLogList($params = array())
    {
        $key = 'nginx_access_log_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 增加停车场队列
     * @param array $params
     * @return string
     */
    public static function updateOrAddPark($params = array()) {
        $key = 'park_info_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    public static function weixinAccessToken() {
        return 'weiXinAccessToken';
    }

    public static function zhaohuToken() {
        return 'zhaoHuToken';
    }

    /**
     * 用于保存车场空车位数量
     * @author kevin
     * @param array $params
     * @return string
     */
    public static function parkNullCarNum($params = array())
    {
        $key = 'park_null_car_num';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 用于保存招行RSA公钥
     */
    public static function zsbankRsaPublicKey()
    {
        return 'zsbank_rsa_public_key';
    }

    /**
     * 用于保存招行免密支付订单查询队列
     */
    public static function zsbankNopwdPayQueryOrderList()
    {
        return 'zsbank_nopwd_pay_query_order_list';
    }

    /**
     * 用于保存APP登录token
     * @param $token
     * @param $app_client_type
     * @return string
     */
    public static function appLoginToken($token,$app_client_type)
    {
        $pre_fix = 'app_login_token_';

        //根据不同终端设置不同前缀 IOS Android web
        if($app_client_type){
            $pre_fix .= $app_client_type;
        }else{
            $pre_fix .= 'web';
        }

        return $pre_fix.'_'.$token;
    }

    /**
     * 用于保存车场信息
     * @param $net_park_id
     * @return string
     */
    public static function parkInfo($net_park_id)
    {
        return  'park_info_'.$net_park_id;
    }

    /**
     * 用于保存CouponType表所有记录
     * @return string
     */
    public static function couponType()
    {
        return  'all_coupon_type';
    }

    /**
     * 用于保存车场与设备终端数量
     * @author kevin
     * @param array $params
     * @return string
     */
    /* public static function websocketClientId($params = array())
    {
        $key = "ws_client";
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    } */


    /**
     * 用于保存查询缴费信息
     * @author kevin
     * @param $user_id
     * @return string
     */
    /* public static function websocketPayInfo($params = array())
    {
        $key = "ws_payinfo";
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    } */

    /**
     * 用于保存车场报表APP登录token
     * @param $user_id
     * @return string
     */
    public static function reportAppLoginToken($user_id)
    {
        $pre_fix = 'report_app_login_token';

        return $pre_fix.'_'.$user_id;
    }

    /**
     * 用于保存微信活动推送任务的task_id
     */
    public static function weixinPushTaskList($params = []) {
        $key = "weixin_push_task_pop";
        if (empty($params)) {
            foreach ($params as $key => $param) {
                $key .= "_{$key}_{$param}";
            }
        }

        return $key;
    }

    /**
     * 用于保存抽奖逻辑业务并发锁限制
     */
    public static function drowLock($drow_activity_id) {
        $key = "drow_lock_{$drow_activity_id}";
        return $key;
    }

    /**
     * 用于保存抽奖商品库存key
     * @return string
     */
    public static function drowStockCacheOfKey($drow_activity_id) {
        $key = "drow_stock_key_{$drow_activity_id}";
        return $key;
    }

    /**
     * 用户保存新充值活动人数
     * @return string
     */
    public static function rechargePeopleNumOfKey() {
        $key = "new_recharge_people";
        return $key;
    }

    /**
     * 免费时间到期key
     * @param array $params
     * @return string
     */
    public static function freeTimeExpire($params = array())
    {
        $key = 'free_time_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 中奖信息key
     * @param array $params
     * @return string
     */
    public static function prizeExpire($params = array())
    {
        $key = 'prize_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 发起人
     * @param array $params
     * @return string
     */
    public static function prizeZhuExpire($params = array())
    {
        $key = 'prize_zhu_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * 助力人
     * @param array $params
     * @return string
     */
    public static function setZhuExpire($params = array())
    {
        $key = 'set_zhu_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 奖品激活
     * @param array $params
     * @return string
     */
    public static function setJiHuoExpire($params = array())
    {
        $key = 'set_ji_huo_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 延时发送入场信息key
     * @param array $params
     * @return string
     */
    public static function carinDelayExpire($params = array())
    {
        $key = 'carin_delay_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 延时发送纸质优惠券key
     * @param array $params
     * @return string
     */
    public static function carinCouponExpire($params = array())
    {
        $key = 'carin_coupon_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 延时发送入场信息下标缓存index_key
     * @param array $params
     * @return string
     */
    public static function carinDelayExpireIndex($params = array())
    {
        $key = 'carin_delay_expire_index';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 延时发送入场信息下标缓存index_key
     * @param array $params
     * @return string
     */
    public static function carinCouponExpireIndex($params = array())
    {
        $key = 'carin_coupon_expire_index';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 延时发送入场信息下标缓存index_key
     * @param array $params
     * @return string
     */
    public static function carinDelayNetparkid($params = array())
    {
        $key = 'carin_delay_netparkid_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * 延时发送入场信息下标缓存index_key
     * @param array $params
     * @return string
     */
    public static function carinCouponNetparkid($params = array())
    {
        $key = 'carin_coupon_netparkid_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }


    /**
     * 世纪恒通入场通知
     * @param array $params
     * @return string
     */
    public static function shiJiCarIn($params = array())
    {
        $key = 'shi_ji_carin_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 注册用户出场发缴费通知
     * @param array $params
     * @return string
     */
    public static function registerCarOut($params = array())
    {
        $key = 'register_car_out_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 出场通知
     * @param array $params
     * @return string
     */
    public static function CarOut($params = array())
    {
        $key = 'car_out_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * 世纪恒通出场通知
     * @param array $params
     * @return string
     */
    public static function shiJiCarOut($params = array())
    {
        $key = 'shi_ji_carout_expire_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }

    /**
     * etc申请成功通知
     * @param array $params
     * @return string
     */
    public static function etcApplySuccess($params = array())
    {
        $key = 'etc_apply_success_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * etc申请失败通知
     * @param array $params
     * @return string
     */
    public static function etcApplyFailed($params = array())
    {
        $key = 'etc_apply_failed_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /**
     * 红包领取成功
     * @param array $params
     * @return string
     */
    public static function redPacketGetSuccess($params = array())
    {
        $key = 'etc_apply_redpacket_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
    /***
     * 邀请成功
     * @param array $params
     * @return string
     */
    public static function invitingSuccess($params = array())
    {
        $key = 'etc_apply_invite_list';
        if (!empty($params)) {
            $key .= '_' . implode('_', $params);
        }
        return $key;
    }
}