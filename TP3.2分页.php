<?php
$User = M('User'); 
$where['status'] = 1;
$count = $User->where($where)->count();
$Page  = new \Think\Page($count,10);
$list = $User->join(C('DB_PREFIX') . 'park p on p.id = um.park_id')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->field('*')->select();
$this->assign('list',$list);
$show  = $Page->show();
$this->assign('page',$show);
$this->display();