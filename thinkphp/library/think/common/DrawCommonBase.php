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
use think\RedisConnector;

/**
 * @公共基础方法
 * @author Suan
 */

class DrawCommonBase {

    ///处理大转盘数据--前端展示
    public function dealDrawwhisper($drawwhisper){
        $data = array();
        $prizeProbability = '';
        $prizeName = '';
        $prizeColor = '';
        $prizeMsg = array();
        if(!empty($drawwhisper)) {
            $count = count($drawwhisper);
            if ($count > 1) {
                foreach ($drawwhisper as $key => $val) {
                    if ($key == 0) {
                        $prizeProbability = '["' . $val['prize_probability'] . '"';
                        $prizeName = '["' . $val['prize_name'] . '"';
                        $prizeColor = '["#fff"';

                    } elseif ($key == $count - 1) {
                        $prizeProbability .= ',"' . $val['prize_probability'] . '"]';
                        $prizeName .= ',"' . $val['prize_name'] . '"]';
                        $prizeColor .= ',"#fff"]';
                    } else {
                        $prizeProbability .= ',"' . $val['prize_probability'] . '"';
                        $prizeName .= ',"' . $val['prize_name'] . '"';
                        $prizeColor .= ',"#fff"';
                    }
                    $prizeMsg[] = $val['receive_rule'];
                }
            } elseif ($count == 1) {
                foreach ($drawwhisper as $key => $val) {
                    $prizeProbability = '["' . $val['prize_probability'] . '"]';
                    $prizeName = '["' . $val['prize_name'] . '"]';
                    $prizeColor = '["#fff"]';
                    $prizeMsg[] = $val['receive_rule'];
                }
            }
        }
        $data['prizeProbability'] = $prizeProbability;
        $data['prizeName'] = $prizeName;
        $data['prizeColor'] = $prizeColor;
        $data['prizeMsg'] = $prizeMsg;
        return $data;
    }


    /*
     * 大转盘抽奖数据
     * $drawwhisper(string)     奖品列表
     * $type(int)     是否安慰奖
     * $isBig(int)     是否提升中奖概率
     */
    public function dealPrizeDrawwhisper($drawwhisper){
        $prize = array();
        if (!empty($drawwhisper)) {
            foreach ($drawwhisper as $key => $val) {
                if ($val['surplus_num'] > 0) {
                    $prize[$key]['prize_info_id'] = $val['prize_info_id'];
                    $prize[$key]['prize_id'] = $val['prize_id'];
                    $prize[$key]['prize_num'] = $val['prize_num'];
                    $prize[$key]['prize_name'] = $val['prize_name'];
                    $prize[$key]['surplus_num'] = $val['surplus_num'];
                    $prize[$key]['prize_img'] = $val['prize_img'];
                    $prize[$key]['prize_probability'] = $val['prize_probability']*10000;
                    $prize[$key]['coupon_rule_id'] = $val['coupon_rule_id'];
                    $prize[$key]['give_card_type'] = $val['give_card_type'];
                    $prize[$key]['platform_type'] = $val['platform_type'];
                    $prize[$key]['prize_info_name'] = $val['prize_info_name'];
                }
            }
        }
        return $prize;
    }
    /*
    public function dealPrizeDrawwhisper($drawwhisper,$type = '',$isBig = '',$startTime=''){
       $start =  strtotime($startTime);
       $prize = array();
       if($type == 1) {
           if (!empty($drawwhisper)) {
               foreach ($drawwhisper as $key => $val) {
                   if ($val['surplus_num'] > 0  && $val['prize_info_name'] == -1) {
                       $prize[$key]['prize_info_id'] = $val['prize_info_id'];
                       $prize[$key]['prize_id'] = $val['prize_id'];
                       $prize[$key]['prize_num'] = $val['prize_num'];
                       $prize[$key]['prize_name'] = $val['prize_name'];
                       $prize[$key]['surplus_num'] = $val['surplus_num'];
                       $prize[$key]['prize_img'] = $val['prize_img'];
                       $prize[$key]['prize_probability'] = $val['prize_probability'];
                   }
               }
           }
       }else{
           $now = time();
           $endData = ceil($now - $start)/(24*60*60);
           if (!empty($drawwhisper)) {
               foreach ($drawwhisper as $key => $val) {
                   if ($val['surplus_num'] > 0) {
                       $prize[$key]['prize_info_id'] = $val['prize_info_id'];
                       $prize[$key]['prize_id'] = $val['prize_id'];
                       $prize[$key]['prize_num'] = $val['prize_num'];
                       $prize[$key]['prize_name'] = $val['prize_name'];
                       $prize[$key]['surplus_num'] = $val['surplus_num'];
                       $prize[$key]['prize_img'] = $val['prize_img'];
                       if($isBig == 1){
                           //前面提高中奖概率3倍
                           if($endData >0 && $endData < 5 ){
                               $prize[$key]['prize_probability'] = $val['prize_probability']*3;
                           }else{
                               $prize[$key]['prize_probability'] = $val['prize_probability'];
                           }

                       }else{
                           $prize[$key]['prize_probability'] = $val['prize_probability'];
                       }
                   }
               }
           }
       }
       return $prize;
    }*/

    //判断是否中过大奖
    public  function isBigPrize($isDraw){
        $flag = 0;
        if(!empty($isDraw)){
            foreach ($isDraw as $key => $val){
                if($val['prize_info_name'] != -1){
                    $flag =  1;
                    break;
                }
            }
        }
        return $flag;
    }

    //奖品列表，剩余数量必须大于0
    public function getPrizeIdwhisper($whisper){
        $prizeIdwhisper = array();
        if(!empty($whisper)){
            foreach ($whisper as $key => $val){
                if($val['surplus_num']>0){
                    $prizeIdwhisper[$val['prize_id']]= $val['prize_probability'];
                }
            }
        }
        return $prizeIdwhisper;
    }

    //通过奖品ID反查获取奖品信息
    public function getPrizeInfo($id,$prizewhisper){
        if(empty($prizewhisper)){
            return false;
        }else{
            foreach ($prizewhisper as $key => $val){
                if($val['prize_id'] == $id){
                    $reArr = $prizewhisper[$key];
                }
            }
        }
        return $reArr;
    }

    //通过奖品返回中奖概率
    public function dealRate($prizewhisper,$id){
        $randomRate = '';

        $count = count($prizewhisper);
        if($count > 0) {
            foreach ($prizewhisper as $key => $val) {
                if($val['prize_id'] == $id){
                    $prize_probability =  1000;
                }else{
                    $prize_probability =  0;
                }
                $randomRate[$key] = $prize_probability;
            }
        }
        return $randomRate;
    }

    /**
     * 中奖业务逻辑处理
     * 返回中奖结果
     */
    public  function drawPriceLogic($data){

        //概率数组的总概率精度
        $proSum = array_sum($data);

        //概率数组循环
        foreach ($data as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);

        return $result;
    }

    /**
     * 获取安慰奖
     * 返回中奖结果
     */
    public  function getAwPrize($data){
        $prize = array();
        if (!empty($drawwhisper)) {
            foreach ($drawwhisper as $key => $val){
                if ($val['surplus_num'] > 0  && $val['prize_info_name'] == -1) {
                    $prize['prize_info_id'] = $val['prize_info_id'];
                    $prize['prize_id'] = $val['prize_id'];
                    $prize['prize_num'] = $val['prize_num'];
                    $prize['prize_name'] = $val['prize_name'];
                    $prize['surplus_num'] = $val['surplus_num'];
                    $prize['prize_img'] = $val['prize_img'];
                    $prize['coupon_rule_id'] = $val['coupon_rule_id'];
                    $prize['prize_probability'] = $val['prize_probability'];
                }
            }
        }
        return $prize;
    }

    //获取用户姓名和电话,没有电话就随机
    public function dealUserInfo($userResult,$openIdResult){
        $reUserInfo = array();
        $count = count($userResult);
        if($count<5 && $count > 0){
            for($i=0;$i<5;$i++){
                $mobile  = '13' . substr(uniqid('', true), 19) . substr(microtime(), 2, 5);
                $reUserInfo[$i]['nick_name'] = $this->getNickName($i);
                $reUserInfo[$i]['mobile'] = substr($mobile, 0, 5).'****'.substr($mobile, 9);
                $reUserInfo[$i]['uid'] = $i;
            }
        }
        if(!empty($userResult)){
            foreach ($userResult as $key =>$val){
                if(!empty($val['mobile'])){
                    $reUserInfo[$val['uid']]['mobile'] = $val['mobile'];
                }else{
                    $arr['mobile']  = '13' . substr(uniqid('', true), 19) . substr(microtime(), 2, 5);
                }
                $reUserInfo[$val['uid']]['mobile'] = substr($reUserInfo[$val['uid']]['mobile'], 0, 5).'****'.substr($reUserInfo[$val['uid']]['mobile'], 9);;
                $reUserInfo[$val['uid']]['uid'] = $val['uid'];
                $reUserInfo[$val['uid']]['nick_name'] = $this->filterEmoji($openIdResult[$val['uid']]['nick_name']);
            }
        }
        return $reUserInfo;
    }

    //处理微信特殊符号
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }

    /**
     * 中奖通知消息提醒解决方法
     * @param string $openid 用户ID
     * @param string $prize 车牌
     * @param string $prize_yes_id 车场联网ID
     * @param int $user_id 用户ID
     * @param string $type 发送结果
     * @param string $lastNum 剩余抽奖次数
     * @param string $activeId 活动ID
     * @param string $user_code 用户id
     */
    public function getPrizeMessage($openid,$prize,$prize_yes_id,$user_id,$type,$activeId='',$lastNum='0',$user_code='0'){
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::prizeExpire();
        $set_value = "{$openid},{$prize},{$prize_yes_id},{$user_id},{$type},{$activeId},{$lastNum}";
        $redis_con->rPush($key, $set_value);
    }

    /**
     * 助力通知消息提醒解决方法发起人
     * @param string $openid 用户ID
     * @param string $num 已助力次数
     * @param string $userwhisper 用户信息
     * @param string $s_activeId 上游活动ID
     */
    public function getZhuMessage($openid,$num,$userwhisper,$s_activeId){
        $user = '';
        if(!empty($userwhisper)){
            foreach ($userwhisper as $key => $val){
                $user .= $val['nick_name'];
            }
        }
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::prizeZhuExpire();
        $set_value = "{$openid},{$num},{$user},{$s_activeId}";
        $redis_con->rPush($key, $set_value);
    }

    /**
     * 助力通知消息提醒解决方法助力人
     * @param string $openid 用户ID
     * @param string $activeId 活动ID
     * @param string $sponsor_user_id 助力发起人ID
     */
    public function setZhuMessage($openid,$activeId,$sponsor_user_id){
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::setZhuExpire();
        $set_value = "{$openid},{$activeId},{$sponsor_user_id}";
        $redis_con->rPush($key, $set_value);
    }

    //处理用户列表
    public function dealUserwhisper($openIdResult,$type = 0){
        $reUserInfo = array();
        if(!empty($openIdResult)){
            foreach ($openIdResult as $key =>$val){
                if(isset($reUserInfo[$val['uid']]['nick_name'])){
                    continue;
                }else{
                    if($type == 1){
                        if (preg_match("/^1[34578]\d{9}$/", $openIdResult[$key]['nick_name'])) {
                            $reUserInfo[$val['uid']]['nick_name'] = substr($openIdResult[$key]['nick_name'], 0, 3) . '****' . substr($openIdResult[$key]['nick_name'], 7);
                        } else {
                            $reUserInfo[$val['uid']]['nick_name'] = $this->filterEmoji($openIdResult[$key]['nick_name']);
                        }
                        $reUserInfo[$val['uid']]['uid'] = $val['uid'];
                    }else {
                        if (preg_match("/^1[34578]\d{9}$/", $openIdResult[$key]['nick_name'])) {
                            $reUserInfo[$val['uid']]['nick_name'] = 'T:' . substr($openIdResult[$key]['nick_name'], 0, 3) . '****' . substr($openIdResult[$key]['nick_name'], 7);
                        } else {
                            $reUserInfo[$val['uid']]['nick_name'] = 'V:' . $this->filterEmoji($openIdResult[$key]['nick_name']);
                        }
                        $reUserInfo[$val['uid']]['uid'] = $val['uid'];
                    }

                }

            }
        }
        return $reUserInfo;
    }
    /***
     * etc申请成功通知
     * @param string $openid 用户openid
     * @param string $id 申请详情ID
     * @param string $nickname 账号名
     * @param string $time 提交的时间
     */
    public function applySuccess($openid,$time=''){
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::etcApplySuccess();
        $set_value = "{$openid},{$time}";
        $redis_con->rPush($key, $set_value);
    }
    /***
     * etc申请失败通知
     * @param string $openid 用户openid
     * @param string $id 申请详情ID
     * @param string $nickname 账号名
     * @param string $remark 失败原因
     */
    public function applyFailed($openid,$id,$mobile,$remark){
        $redis_con = RedisConnecter::connRedis('whisper');
        //var_dump($redis_con->dump('etc_apply_failed_whisper'));exit;
        $key = ShowRedisKey::etcApplyFailed();
        $set_value = "{$openid},{$id},{$mobile},{$remark}";
        $redis_con->rPush($key, $set_value);
    }
    /***
     * 余额红包领取成功
     * @param string $openid 用户openid
     * @param string $redpacket_name 红包名称
     * $friend_name 好友名称
     */
    public function redPacketGetSuccess($openid,$packet_name,$friend_name){
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::redPacketGetSuccess();
        $set_value = "{$openid},{$packet_name},{$friend_name}";
        $redis_con->rPush($key, $set_value);
    }
    /***
     * 余额红包邀请成功通知
     * @param string $openid 用户openid
     * @param string $account 被邀请者
     * $friend_name 好友昵称
     * $pack_name 红包名称
     */
    public function invitingSuccess($openid,$account,$friend_name,$pack_name){
        $redis_con = RedisConnecter::connRedis('whisper');
        $key = ShowRedisKey::invitingSuccess();
        $set_value = "{$openid},{$account},{$friend_name},{$pack_name}";
        $redis_con->rPush($key, $set_value);
    }
}