<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
class App 
{
    public function list()
    {
		$udid = isset($_GET['udid'])?$_GET['udid']:'';
		if($udid == '') {$udid = 0;}//return json(['code'=>0,'msg'=>'请上传参数UDID']);
		$kcode = isset($_GET['code'])?$_GET['code']:'';
		if($kcode == ''){
			$chkif = Db::table('fa_kami')->where('udid',$udid)->order('id desc')->select();
			if($chkif){
				$ifend = time() > $chkif[0]['endtime']?true:false;
				$config = Db::table('fa_config')->select();
				if(empty($config)) return json(['code'=>0,'msg'=>'暂无站点数据']);
				$list = Db::table('fa_category')->where('status','normal')->order('weigh desc')->select();
				if(empty($list)) return json(['code'=>0,'msg'=>'暂无app数据']);
				$data = [];
				foreach ($list as $key=>$val)
				{
					$data[$key]['name'] = $val['name'];
					$data[$key]['version'] = $val['nickname'];
					$data[$key]['versionDate'] = date('Y-m-d',$val['updatetime']);
					$data[$key]['versionDescription'] = str_replace("\\n",'N',$val['keywords']);
					$data[$key]['lock'] = $val['bt2b'];
					$data[$key]['downloadURL'] = $ifend?'':$val['bt1a'];
					$data[$key]['isLanZouCloud'] = $val['flag'];
					$data[$key]['iconURL'] = $val['image'];
					$data[$key]['tintColor'] = $val['bt1b'];
					$data[$key]['size'] = $val['bt2a'];
				}
				foreach ($config as $key=>$val)
				{
					if($val['name'] == 'name') $info['name'] = $val['value'];
					if($val['name'] == 'message') $info['message'] = $val['value'];
					if($val['name'] == 'identifier') $info['identifier'] = $val['value'];
					if($val['name'] == 'sourceURL') $info['sourceURL'] = $val['value'];
					if($val['name'] == 'sourceicon') $info['sourceicon'] = $val['value'];
					if($val['name'] == 'payURL') $info['payURL'] = $val['value'];
					if($val['name'] == 'unlockURL') $info['unlockURL'] = $val['value'];
				}
				$arr = [
					'name'=>$info['name'],
					'message'=>$info['message'],
					'identifier'=>$info['identifier'],
					'sourceURL'=>$info['sourceURL'],
					'sourceicon'=>$info['sourceicon'],
					'payURL'=>$info['payURL'],
					'unlockURL'=>$info['unlockURL'],
					'apps'=>$data
					];
				$json = json_encode($arr,320);
				//halt($json);
				$jsonStr  = str_replace('N', '\n', $json);
				return $jsonStr;
			}else{
				$config = Db::table('fa_config')->select();
				if(empty($config)) return json(['code'=>0,'msg'=>'暂无站点数据']);
				$list = Db::table('fa_category')->where('status','normal')->order('weigh desc')->select();
				if(empty($list)) return json(['code'=>0,'msg'=>'暂无app数据']);
				$data = [];
				foreach ($list as $key=>$val)
				{
					$data[$key]['name'] = $val['name'];
					$data[$key]['version'] = $val['nickname'];
					$data[$key]['versionDate'] = date('Y-m-d',$val['updatetime']);
					$data[$key]['versionDescription'] = str_replace("\\n",'N',$val['keywords']);
					$data[$key]['lock'] = $val['bt2b'];
					$data[$key]['downloadURL'] = $val['bt2b']?'':$val['bt1a'];
					$data[$key]['isLanZouCloud'] = $val['flag'];
					$data[$key]['iconURL'] = $val['image'];
					$data[$key]['tintColor'] = $val['bt1b'];
					$data[$key]['size'] = $val['bt2a'];
				}
				foreach ($config as $key=>$val)
				{
					if($val['name'] == 'name') $info['name'] = $val['value'];
					if($val['name'] == 'message') $info['message'] = $val['value'];
					if($val['name'] == 'identifier') $info['identifier'] = $val['value'];
					if($val['name'] == 'sourceURL') $info['sourceURL'] = $val['value'];
					if($val['name'] == 'sourceicon') $info['sourceicon'] = $val['value'];
					if($val['name'] == 'payURL') $info['payURL'] = $val['value'];
					if($val['name'] == 'unlockURL') $info['unlockURL'] = $val['value'];
				}
				$arr = [
					'name'=>$info['name'],
					'message'=>$info['message'],
					'identifier'=>$info['identifier'],
					'sourceURL'=>$info['sourceURL'],
					'sourceicon'=>$info['sourceicon'],
					'payURL'=>$info['payURL'],
					'unlockURL'=>$info['unlockURL'],
					'apps'=>$data
					];
				$json = json_encode($arr,320);
				//halt($json);
				$jsonStr  = str_replace('N', '\n', $json);
				return $jsonStr;
			}
		}else{
			$chkis = Db::table('fa_kami')->where('kami',$kcode)->order('id desc')->select();
			if($chkis){
				$kdata = $chkis[0];
				if(intval($kdata['jh'])){
					return json(['code'=>0,'msg'=>'解锁码已使用']);
				}else{
					//---
					$kmtp = intval($kdata['kmyp']);
					if($kmtp == 1){ $sydt = time(); $endtm = $sydt+(86400*30); }
					if($kmtp == 2){ $sydt = time(); $endtm = $sydt+(86400*30*3); }
					if($kmtp == 3){ $sydt = time(); $endtm = $sydt+(86400*30*12); }
					Db::table('fa_kami')->where('id', $kdata['id'])->update(array('udid'=>$udid, 'usetime'=>$sydt, 'endtime'=>$endtm, 'jh'=>1));
					return json(['code'=>0,'msg'=>'ok，解锁成功']);
				}
			}else{
				return json(['code'=>0,'msg'=>'解锁码不存在']);
			}
		}
    }
}