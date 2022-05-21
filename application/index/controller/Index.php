<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
    	if(!empty($_POST['uid'])){
    		$id = (int)$_POST['uid'];
    		$time = (int)date('d',time());
    		$s = DB::name('category')->where('id',$id)->where('cstime',$time)->find();
    		if($s){
    			DB::name('category')->where('id',$id)->setInc('cs');
    		}else{
    			DB::name('category')->where('id',$id)->update([
    				'cs' => 1,
    				'cstime' => $time
    			]);
    		}
    		return 'ok';
    	}
    	$data['img'] = DB::name('attachment')->whereNotNull('urls')->select();
    	$data['category'] = DB::name('category')->where('pid',0)->where('status','normal')->order('weigh desc')->select();
    	$data['xm'] = [];
    	foreach ($data['category'] as $v){
    		$data['xm'][$v['id']] = DB::name('category')->where('pid',$v['id'])->where('status','normal')->order('weigh desc')->select();
    	}
        return $this->view->fetch('',$data);
    }

}
