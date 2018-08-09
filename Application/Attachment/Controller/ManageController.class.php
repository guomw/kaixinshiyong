<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Attachment\Controller;
use \Common\Controller\BaseController;
use \Attachment\Library\attachment;
Class AttachmentController extends BaseController {
    public function _initialize() {
    	parent::_initialize();
    	$this->db = D('Attachment');
        $this->attachment = new attachment();
        $this->modules =  F('Module');
        $this->categorys = F('Category');
        $this->upload_url = './uploadfile/';
        $this->upload_path = SITE_PATH.'/uploadfile/';
    }

    /**
     * 数据库模式
     * @author   <jason>
     */
    public function init() {
        $pagecurr = max(1, I('page', 0, 'intval'));
        $pagesize = 20;
        /* 多条件搜索附件 */
        $map = array();        
        if (isset($_POST) && !empty($_POST)) {
            $info = dhtmlspecialchars($_POST['info']);
            $info['start_uploadtime'] = (!empty($info['start_uploadtime'])) ? strtotime(trim($info['start_uploadtime'])) : 0;
            $info['end_uploadtime'] = (!empty($info['end_uploadtime'])) ? strtotime(trim($info['end_uploadtime'])) : 0;
            $info['fileext'] = trim($info['fileext']);

            if ($info['filename']) {
                $map['filename'] = array("LIKE", "%".$info['filename']."%");
            }
            if ($info['start_uploadtime']) {
                $map['uploadtime'] = array("EGT", $info['start_uploadtime']);
            }
            if ($info['end_uploadtime']) {
                $MAP['uploadtime'] = array("ELT", $info['end_uploadtime']);
            }
            if ($info['fileext']) {
                $map['fileext'] = array("LIKE", "%".$info['fileext']."%");
            }
        }
        $count = $this->db->where($map)->count();
        $lists = $this->db->where($map)->page($pagecurr, $pagesize)->select();
        $pages = page($count, $pagesize);

        $this->form = new \Common\Library\form();
        /* 模板赋值 */
        $this->assign('info', $info);
        $this->assign('count', $count);
        $this->assign('lists', $lists);
        $this->assign('pages', $pages);
    	$this->display();
    }

    /**
     * 目录式
     * @author   <jason>
     */
    public function dir() {
        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\'), '', trim($_GET['dir'])) : '';
        $filepath = $this->upload_path.$dir;
        $list = glob($filepath.'/'.'*');
        if(!empty($list)) rsort($list);
        $local = str_replace(SITE_PATH, '', $filepath);     
        $url = ($dir == '.' || $dir=='') ? $this->upload_url : $this->upload_url.str_replace('.', '', $dir).'/';

        $this->assign('local', $local);
        $this->assign('url', $url);
        $this->assign('dir', $dir);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 文件上传
     * @author   <jason>
     */
    public function upload() {
        
    }

    /**
     * 文件删除
     * @author   < @ .com>
     */
    public function delete() {
        if (!submitcheck('dosubmit')) {
            $this->error('请勿非法访问');
        }
        $aids = (array) $_POST['aid'];
        if(empty($aids)) $this->error('未指定删除的附件');
        foreach ($aids as $aid) {
            if(!is_numeric($aid)) continue;
            $filepath = $this->db->getFieldByAid($aid, 'filepath');
            $filepath = (!empty($filepath)) ? $this->upload_path.$filepath : '';
            if (file_exists($filepath)) {
                // 删除各种尺寸的缩略图
                $lists = glob(dirname($filepath).'/*'.basename($filepath));
                foreach ($lists as $f) {
                    @unlink($f);
                }
            }
            $this->db->delete($aid);
        }
        $this->success('操作成功');
    }

    /**
     * 目录式文件删除
     * @author   <jason>
     */
    public function delete_dir() {
        if (submitcheck('dosubmit')) {
            $dir = I('dir');
            $filename = I('filename');
            if (empty($dir) || empty($filename)) $this->error('参数错误');
            $file = $this->upload_path.urldecode($dir).'/'.$filename;
            if (!file_exists($file)) $this->error('文件不存在');
            @unlink($file);
            $this->success('指定文件删除成功');
        } else {
            $this->error('请勿非法提交');
        }
    }

}