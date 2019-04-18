<?php

/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require IA_ROOT . '/addons/ewei_shopv2/defines.php';
require EWEI_SHOPV2_INC . 'plugin_processor.php';
require_once EWEI_SHOPV2_INC . 'receiver.php';

class PosteraProcessor extends PluginProcessor {

	public function __construct() {
		parent::__construct('postera');
	}

	public function respond($obj = null) {


		global $_W;
		$message = $obj->message;
		$msgtype = strtolower($message['msgtype']);
		$event = strtolower($message['event']);

		//更新用户信息
		$obj->member = $this->model->checkMember($message['from']);

		if ($msgtype == 'text' || $event == 'click') {

			return $this->responseText($obj);
		} else if ($msgtype == 'event') {
			if ($event == 'scan') {

				//扫描
				return $this->responseScan($obj);
			} else if ($event == 'subscribe') {
 
				//关注
				return $this->responseSubscribe($obj);
			}
		}
	}

	private function responseText($obj) {
 
		
		global $_W;
		//url调用，避免5秒超时返回
		$timeout = 4;
		load()->func('communication');
		$url = mobileUrl('postera/build',array('openid' => $obj->message['from'], 'content' => urlencode($obj->message['content']),'timestamp'=>TIMESTAMP),true);
		$resp = ihttp_request($url, array(), array(), $timeout);
		return $this->responseEmpty();
	}

	private function responseEmpty() {
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}

	private function responseDefault($obj) {
		global $_W;
		//未找到推荐人， 查找默认回复信息
		return $obj->respText('感谢您的关注!');
	}

	private function responseScan($obj) {
		global $_W;

		$openid = $obj->message['from'];
		$sceneid = $obj->message['eventkey'];
		$ticket = $obj->message['ticket'];

		if (empty($ticket)) {
			return $this->responseDefault($obj);
		}
	
	
		
		$qr = $this->model->getQRByTicket($ticket);
		if (empty($qr)) {
			return $this->responseDefault($obj);
		}

		$poster = pdo_fetch('select * from ' . tablename('ewei_shop_postera') . ' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$qr['posterid'], ':uniacid' => $_W['uniacid']));
		if (empty($poster)) {
			return $this->responseDefault($obj);
		}

		//推荐者
		$qrmember = m('member')->getMember($qr['openid']);

		//分销
		$this->commission($poster, $obj->member, $qrmember);

		$url = trim($poster['respurl']);
		if (empty($url)) {
			if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {
				$url = mobileUrl('commission/myshop',array('mid'=>$qrmember['id']));
			} else {
				$url = mobileUrl('',array('mid'=>$qrmember['id']));
			}
		}

        if ($poster['resptype'] == '0')
        {
            if(!empty($poster['resptitle'])){
                $news = array(array('title' => $poster['resptitle'], 'description' => $poster['respdesc'], 'picurl' => tomedia($poster['respthumb']), 'url' => $url));
                return $obj->respNews($news);
            }else if($poster['type']==3){

                $goods = pdo_fetch('select id,title, thumb,description from '.tablename('ewei_shop_goods').' where id=:id limit 1',array(':id'=>$poster['goodsid']));

                if(!empty($goods)) {
                    $url = mobileUrl('goods/detail', array('id' => $goods['id'], 'mid' => $qrmember['id']));
                    $news = array(array('title' => $goods['title'], 'description' => $goods['description'], 'picurl' => tomedia($goods['thumb']), 'url' => $url));
                    return $obj->respNews($news);
                }
            }
        }
        if ($poster['resptype'] == '1')
        {
            if(!empty($poster['resptext'])){
                return $obj->respText($poster['resptext']);
            }else if($poster['type']==3){

                $goods = pdo_fetch('select id,title, thumb,description from '.tablename('ewei_shop_goods').' where id=:id limit 1',array(':id'=>$poster['goodsid']));
                if(!empty($goods)) {
                    $url = mobileUrl('goods/detail', array('id' => $goods['id'], 'mid' => $qrmember['id']));
                    return $obj->respText( "<a href=\"{$url}\">". $goods['title'] ."</a>\n".$goods['description'] );
                }

            }
        }
		return $this->responseEmpty();
	}

	private function responseSubscribe($obj) {
		global $_W;
		$openid = $obj->message['from'];
		$keys = explode('_', $obj->message['eventkey']);
		$sceneid = isset($keys[1]) ? $keys[1] : '';
		$ticket = $obj->message['ticket'];
		$member = $obj->member;

        $receiver = new Receiver();
        $receiver->saleVirtual($obj);
		if (empty($ticket)) {
			return $this->responseDefault($obj);
		}
 
		
		$qr = $this->model->getQRByTicket($ticket);
		if (empty($qr)) {
			return $this->responseDefault($obj);
		}

		$poster = pdo_fetch('select * from ' . tablename('ewei_shop_postera') . ' where id=:id and uniacid=:uniacid limit 1', array(':id'=>$qr['posterid'], ':uniacid' => $_W['uniacid']));
		
		if (empty($poster)) {
			return $this->responseDefault($obj);
		}
 
		//推荐者
		$qrmember = m('member')->getMember($qr['openid']);

		//检测日志
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_postera_log') . ' where openid=:openid and posterid=:posterid and uniacid=:uniacid limit 1', array(':openid' => $openid, ':posterid' => $poster['id'], ':uniacid' => $_W['uniacid']));
		if (empty($log) && $openid != $qr['openid']) {
			$log = array('uniacid' => $_W['uniacid'], 'posterid' => $poster['id'], 'openid' => $openid, 'from_openid' => $qr['openid'], 'subcredit' => $poster['subcredit'], 'submoney' => $poster['submoney'], 'reccredit' => $poster['reccredit'], 'recmoney' => $poster['recmoney'], 'createtime' => time());
			pdo_insert('ewei_shop_postera_log', $log);
			$log['id'] = pdo_insertid();

			//关注者入账描述
			$subpaycontent = $poster['subpaycontent'];
			if (empty($subpaycontent)) {
				$subpaycontent = '您通过 [nickname] 的推广二维码扫码关注的奖励';
			}
			$subpaycontent = str_replace("[nickname]", $qrmember['nickname'], $subpaycontent);

			//推荐者入账描述
			$recpaycontent = $poster['recpaycontent'];
			if (empty($recpaycontent)) {
				$recpaycontent = '推荐 [nickname] 扫码关注的奖励';
			}
			$recpaycontent = str_replace("[nickname]", $member['nickname'], $subpaycontent);

			//推荐者
			//第一次扫描,赠送积分
			if ($poster['subcredit'] > 0) {
				//关注者积分
				m('member')->setCredit($openid, 'credit1', $poster['subcredit'], array(0, '扫码关注积分+' . $poster['subcredit']));
			}
			if ($poster['submoney'] > 0) {
				//关注者奖励
				$pay = $poster['submoney'];
                if ($poster['paytype'] == 1) {
                    $pay *= 100;
                    $res = m('finance')->payRedPack($openid, $pay,date('YmdHis').random(6,true), array(), $subpaycontent,array());
                }else{
                    $res = m('finance')->pay($openid, $poster['paytype'], $pay, '', $subpaycontent,false);
                }
			}

            $reward_totle = !empty($poster['reward_totle'])?json_decode($poster['reward_totle'],true):array();

            $reward_real = pdo_fetch('select sum(reccredit) as reccredit_totle,sum(recmoney) as recmoney_totle,sum(reccouponnum) as reccouponnum_totle  from ' . tablename('ewei_shop_postera_log') . ' where from_openid=:fromopenid and posterid=:posterid and uniacid=:uniacid and createtime between :time1 and :time2 limit 1', array(':fromopenid' => $qr['openid'], ':posterid' => $poster['id'], ':uniacid' => $log['uniacid'],':time1'=>strtotime(date('Y-m',time())."-1"),':time2'=>time()));

            if (empty($reward_totle['reccredit_totle']) || intval($reward_totle['reccredit_totle']) >= intval($reward_real['reccredit_totle'])){
                if ($poster['reccredit'] > 0) {
                    //推荐者积分
                    m('member')->setCredit($qr['openid'], 'credit1', $poster['reccredit'], array(0, '推荐扫码关注积分+' . $poster['reccredit']));
                }
            }
            if (empty($reward_totle['recmoney_totle']) || floatval($reward_totle['recmoney_totle']) >= floatval($reward_real['recmoney_totle'])){
                if ($poster['recmoney'] > 0) {
                    //推荐者钱
                    $pay = $poster['recmoney'];
                    if ($poster['paytype'] == 1) {
                        $pay *= 100;
                        $res = m('finance')->payRedPack($qr['openid'], $pay,date('YmdHis').random(6,true), array(), $recpaycontent,array());
                    }else{
                        $res = m('finance')->pay($qr['openid'], $poster['paytype'], $pay, '', $recpaycontent,false);
                    }
                }
            }
			
			//赠送优惠券
			$cansendreccoupon =false;
			$cansendsubcoupon =false;
			$plugin_coupon = com('coupon');
			if($plugin_coupon){
				//推荐者奖励
                if (empty($reward_totle['reccouponnum_totle']) || intval($reward_totle['reccouponnum_totle']) >= intval($reward_real['reccouponnum_totle'])){
                    if(!empty($poster['reccouponid']) && $poster['reccouponnum']>0){
                        $reccoupon = $plugin_coupon->getCoupon($poster['reccouponid']);
                        if(!empty($reccoupon)){
                            $cansendreccoupon = true;
                        }
                    }
                }

				//关注者奖励
				if(!empty($poster['subcouponid']) && $poster['subcouponnum']>0){
					$subcoupon = $plugin_coupon->getCoupon($poster['subcouponid']);
 					if(!empty($subcoupon)){
						$cansendsubcoupon = true;
					}
				}
			}
			
			if (!empty($poster['subtext'])) {
				//推荐人奖励通知
				$subtext = $poster['subtext'];
				$subtext = str_replace("[nickname]", $member['nickname'], $subtext);
				$subtext = str_replace("[credit]", $poster['reccredit'], $subtext);
				$subtext = str_replace("[money]", $poster['recmoney'], $subtext);
				if($reccoupon){
					$subtext = str_replace("[couponname]", $reccoupon['couponname'], $subtext);
					$subtext = str_replace("[couponnum]", $poster['reccouponnum'], $subtext);
				}
				

				if (!empty($poster['templateid'])) {
					m('message')->sendTplNotice($qr['openid'], $poster['templateid'], array(
						'first' => array('value' => "推荐关注奖励到账通知", "color" => "#4a5077"),
						'keyword1' => array('value' => '推荐奖励', "color" => "#4a5077"),
						'keyword2' => array('value' => $subtext, "color" => "#4a5077"),
						'keyword3' => array('value' => date('Y-m-d H:i:s'), "color" => "#4a5077"),
						'remark' => array('value' => "\r\n谢谢您对我们的支持！", "color" => "#4a5077"),), '');
				} else {
					m('message')->sendCustomNotice($qr['openid'], $subtext);
				}
			}

			if (!empty($poster['entrytext'])) {
				//关注者奖励通知
				$entrytext = $poster['entrytext'];
				$entrytext = str_replace("[nickname]", $qrmember['nickname'], $entrytext);
				$entrytext = str_replace("[credit]", $poster['subcredit'], $entrytext);
				$entrytext = str_replace("[money]", $poster['submoney'], $entrytext);

				if($subcoupon){
					$entrytext = str_replace("[couponname]", $subcoupon['couponname'], $entrytext);
					$entrytext = str_replace("[couponnum]", $poster['subcouponnum'], $entrytext);
				}
				
				if (!empty($poster['templateid'])) {
					m('message')->sendTplNotice($openid, $poster['templateid'], array(
						'first' => array('value' => "关注奖励到账通知", "color" => "#4a5077"),
						'keyword1' => array('value' => '关注奖励', "color" => "#4a5077"),
						'keyword2' => array('value' => $entrytext, "color" => "#4a5077"),
						'keyword3' => array('value' => date('Y-m-d H:i:s'), "color" => "#4a5077"),
						'remark' => array('value' => "\r\n谢谢您对我们的支持！", "color" => "#4a5077"),), '');
				} else {
					m('message')->sendCustomNotice($openid, $entrytext);
				}
			}
			
			$upgrade = array();
			if($cansendreccoupon){
				$upgrade['reccouponid'] = $poster['reccouponid'];
				$upgrade['reccouponnum'] = $poster['reccouponnum'];
				$plugin_coupon->poster($qrmember, $poster['reccouponid'],$poster['reccouponnum'],5);
			}
			if($cansendsubcoupon){
				$upgrade['subcouponid'] = $poster['subcouponid'];
				$upgrade['subcouponnum'] = $poster['subcouponnum'];
				$plugin_coupon->poster($member, $poster['subcouponid'],$poster['subcouponnum'],5);
			}
			if(!empty($upgrade)){
				pdo_update('ewei_shop_postera_log',$upgrade,array('id'=>$log['id']));
			}
		}

		//分销
		$this->commission($poster, $member, $qrmember);

		$url = trim($poster['respurl']);
		if (empty($url)) {
			if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {
				$url = mobileUrl('commission/myshop',array('mid'=>$qrmember['id']));
			} else {
				$url = mobileUrl('',array('mid'=>$qrmember['id']));
			}
		}

		if ($poster['resptype'] == '0')
		{
			if(!empty($poster['resptitle'])){
				$news = array(array('title' => $poster['resptitle'], 'description' => $poster['respdesc'], 'picurl' => tomedia($poster['respthumb']), 'url' => $url));
				return $obj->respNews($news);
			}else if($poster['type']==3){

			    $goods = pdo_fetch('select id,title, thumb,description from '.tablename('ewei_shop_goods').' where id=:id limit 1',array(':id'=>$poster['goodsid']));

			    if(!empty($goods)) {
                    $url = mobileUrl('goods/detail', array('id' => $goods['id'], 'mid' => $qrmember['id']));
                    $news = array(array('title' => $goods['title'], 'description' => $goods['description'], 'picurl' => tomedia($goods['thumb']), 'url' => $url));
                    return $obj->respNews($news);
                }
            }
		}
		if ($poster['resptype'] == '1')
		{
			if(!empty($poster['resptext'])){
				return $obj->respText($poster['resptext']);
			}else if($poster['type']==3){

                $goods = pdo_fetch('select id,title, thumb,description from '.tablename('ewei_shop_goods').' where id=:id limit 1',array(':id'=>$poster['goodsid']));
                if(!empty($goods)) {
                    $url = mobileUrl('goods/detail', array('id' => $goods['id'], 'mid' => $qrmember['id']));
                    return $obj->respText( "<a href=\"{$url}\">". $goods['title'] ."</a>\n".$goods['description'] );
                }

            }
		}
		return $this->responseEmpty();
	
	}

	private function commission($poster, $member, $qrmember) {

		$time = time();

		$p = p('commission');
		if ($p) {
			$cset = $p->getSet();
			if (!empty($cset)) {
				if ($member['isagent'] != 1) {//如果扫码会员不是分销商或准分销商，且没有上线
					if ($qrmember['isagent'] == 1 && $qrmember['status'] == 1) {//如果推荐人分销商
						if (!empty($poster['bedown'])) {//如果扫码成为下线
							if (empty($member['agentid'])) {
								if(empty($member['fixagentid'])){
									//上级是分销商,扫码成为下线，没有上线
									$member['agentid'] = $qrmember['id'];
                                    $authorid = empty($qrmember['isauthor']) ? $qrmember['authorid'] : $qrmember['id'];
                                    $author = p('author');
                                    if ($author) {
                                        $p->upgradeLevelByAgent($qrmember['id']);
                                        pdo_update('ewei_shop_member', array('agentid' => $qrmember['id'], 'childtime' => $time,'authorid'=>$authorid), array('id' => $member['id']));
                                    }else{
                                        pdo_update('ewei_shop_member', array('agentid' => $qrmember['id'], 'childtime' => $time), array('id' => $member['id']));
                                    }

									//发送增加通知
									$p->sendMessage($qrmember['openid'], array('nickname' => $member['nickname'], 'childtime' => $time), TM_COMMISSION_AGENT_NEW);

									//检测升级
									$p->upgradeLevelByAgent($qrmember['id']);

									//股东升级
									if(p('globonus')){
										p('globonus')->upgradeLevelByAgent($qrmember['id']);
									}
									//区域代理升级
									if (p('abonus')) {
										p('abonus')->upgradeLevelByAgent($qrmember['id']);
									}
									//创始人升级
									if(p('author')){
										p('author')->upgradeLevelByAgent($qrmember['id']);
									}
								}
							} 

						}
                        //判断是否直接成为分销商
                        if (!empty($poster['beagent'])) {
                            $become_check = intval($cset['become_check']);
                            pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => $become_check, 'agenttime' => $time), array('id' => $member['id']));
                            if ($become_check == 1) {

                                $p->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
                                //检测升级
                                $p->upgradeLevelByAgent($qrmember['id']);

                                //股东升级
                                if(p('globonus')){
                                    p('globonus')->upgradeLevelByAgent($qrmember['id']);
                                }
                                //区域代理升级
                                if (p('abonus')) {
                                    p('abonus')->upgradeLevelByAgent($qrmember['id']);
                                }
                                //创始人升级
                                if(p('author')){
                                    p('author')->upgradeLevelByAgent($qrmember['id']);
                                }
                            }
                        }
					}
				}
			}
		}
	}

}
