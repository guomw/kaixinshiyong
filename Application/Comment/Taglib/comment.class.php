<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class comment { 
    function __construct() {
        $this->categorys = getcache('category','commons');
        $this->models = getcache('model','commons');
        $this->db = D('Comment');
    }

    public function count() {
        $sqlMap = array();
        $sqlMap['status'] = 1;
        $sqlMap['commentid'] = 'document_';
        $catid = (int) $data['catid'];
        $id = (int) $data['id'];
        if ($catid < 1 || $id < 1) {
            echo "0";
            exit();
        }
        $modelid = $this->categorys[$catid]['modelid'];
        if ($modelid > 1) {
            $sqlMap['commentid'] .= $modelid;
        } else {
            $sqlMap['commentid'] .= '%';
        }
        $sqlMap['commentid'] .= '_'.$catid.'_'.$id;
        $count = $this->db->where($sqlMap)->order($data['order'])->limit($data['limit'])->count();
        echo $count;
        exit();
    }

    public function lists($data) {
    	$sqlMap = array();
    	$sqlMap['status'] = 1;
    	$sqlMap['commentid'] = 'document_';
    	$catid = (int) $data['catid'];
    	$id = (int) $data['id'];
    	if ($catid < 1 || $id < 1) {
    		return FALSE;
    	}
    	$modelid = $this->categorys[$catid]['modelid'];
    	if ($modelid > 1) {
    		$sqlMap['commentid'] .= $modelid;
    	} else {
    		$sqlMap['commentid'] .= '%';
    	}
    	$sqlMap['commentid'] .= '_'.$catid.'_'.$id;
    	$lists = $this->db->where($sqlMap)->order($data['order'])->limit($data['limit'])->select();
    	return $lists;
    }
}