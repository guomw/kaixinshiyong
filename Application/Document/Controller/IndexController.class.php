<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use \Common\Controller\BaseController;
define('MODULE_CACHE', DATA_PATH.'caches_model/');
Class IndexController extends BaseController {
    public function _initialize() {
    	parent::_initialize();
        $this->categorys = getcache('category', 'commons');
        $this->models = getcache('model', 'commons');
        /* 将页码变量赋值到模板 */
    }

    /**
     * 主页
     * @author   <jason>
     */
    public function index() {
        $SEO = seo();
        include template('index');
    }
    /**
     * 搜索功能
     */
    public function search(){
    	if(submitcheck('dosubmit')){
    		$where = '';
    		$keywords = I('keywords', '', 'remove_xss');
    		if(!empty($keywords) && $keywords == '请输入关键字、图片编号﻿'){
    			$keywords = '';
    		}
    		$where .= "title LIKE '%".$keywords."%'";
    		$this->lists(12, 'new', $where);
    	}
    }

    public function search_help(){
            $catid = (int) I('catids',1);
            $where = '';
            $keywords = I('keywords');
            if(!empty($keywords) && $keywords == '请输入关键字、图片编号﻿'){
                $keywords = '';
            }
            $where .= "title LIKE '%".$keywords."%'";
            $info['keywords'] = $keywords;
            $info['where'] = $where;
            $this->lists($catid, 'search_help', $info);

   }

   public function answer(){
     $this->lists(1, 'answer_list');

   }
    /**
     * 列表页
     * @author   <jason>
     */
    public function lists($catid = 0, $tpl = '', $extra = '') {  
        $catid = (int) $catid;
        $page = max(1, (int) I('page'));
        if ($catid < 1 || !isset($this->categorys[$catid])) $this->error('栏目不存在');
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $category['setting'] = string2array($category['setting']);        
        $arrparentid = explode(',', $category['arrparentid']);
        $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
        if (in_array(2, $arrparentid) || $catid == 2) {
            $t_title='试客中心';
        }else{
             $t_title='商家中心';
        }
               


        $name = model('category')->find($catid);
        $s =  model('category')->where('parentid='.$catid)->find();
        $catename = model('category')->where('catid='.$name['parentid'])->find();
        
        $setting = $category['setting'];
        $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords']);
        extract($category);

        if ($extra) {
             $count = model('article')->where($extra['where'])->count();
             $article = model('article')->where($extra['where'])->select();
             $condition .= "title LIKE '%订单记录%'";
             $order = model('article')->where($condition)->select();
             $keywords = $extra['keywords'];
        }
        /* 定义模板 */
        if ($category['type'] == 0) {
            if ($category['child'] == 1) {
                $tplname = ($category['setting']['category_template']) ? $category['setting']['category_template'] : 'category';
            } else {
                $tplname = ($category['setting']['list_template']) ? $category['setting']['list_template'] : 'list';
            }
        } else {
            $this->page_db = D('page');
            $r = $this->page_db->where(array('catid' => $catid))->find();
            if($r) extract($r);
            $tplname = ($category['setting']['page_template']) ? $category['setting']['page_template'] : 'page';
            $keywords = $keywords ? $keywords : $setting['meta_keywords'];
            $SEO = seo($catid, $title,$setting['meta_description'],$keywords);
        }        
        $arrchild_arr = $this->categorys[$parentid]['arrchildid'];
        if($arrchild_arr=='') $arrchild_arr = $this->categorys[$catid]['arrchildid'];
        $arrchild_arr = explode(',',$arrchild_arr);
        array_shift($arrchild_arr);        
        /* 附加搜索商家条件 2014.06.29 */
        $where = ($extra) ? $extra : '1=1';
        //每页显示的条数
        $num = I('pagesize', 20, 'intval');
        $tplname = (!empty($tpl)) ? $tpl : $tplname;
        include template($tplname);
    }

    /**
     * 内容页浏览
     * @author   <jason>
     */
    public function show($catid = 0, $id = 0) {
        $id = (int) $id;
        $catid = (int) $catid;
    	if ($catid < 1 || $id < 1) {
    		$this->error('参数错误');
    	}
    	$category = $this->categorys[$catid];
        $parent= model('category')->where(array('catid'=>$catid))->find();
        $modelid = $category['modelid'];
    	$model = $this->models[$category['modelid']];
        if (!$category || !$model) {
            $this->error('数据异常');            
        }
        $category['setting'] = string2array($category['setting']);
        $arrparentid = explode(',', $category['arrparentid']);
         if (in_array(2, $arrparentid) || $catid == 2) {
            $t_title='试客中心';
        }else{
             $t_title='商家中心';
        }

        
        $this->catid = $catid;
        $this->id = $id;

    	$tablename = $model['tablename'];
    	$tablename_data = $tablename.'_data';
    	//------ 查询文档内容 -------
    	$rs = M($tablename)->where(array('id' => $id))->find();
    	$rs2 = M($tablename_data)->where(array('id' => $id))->find();
    	if (!$rs || !$rs2) {
    		$this->error('您查看的记录不存在');
    	}
    	$data = array_merge($rs, $rs2);
        $where .= "title LIKE '%订单记录%'";
        $article = model('article')->where($where)->select();

        if ($data['status'] != 1) $this->error('该信息尚未审核，禁止浏览');
        $pages = '';
        if($data['paginationtype'] != 0) {
            //手动分页
            $CONTENT_POS = strpos($content, '[page]');
            if($CONTENT_POS !== false) {
                $this->url = new \Common\Library\url();
                $contents = array_filter(explode('[page]', $content));
                $pagenumber = count($contents);
                if (strpos($content, '[/page]')!==false && ($CONTENT_POS<7)) {
                    $pagenumber--;
                }
                for($i=1; $i<=$pagenumber; $i++) {
                    $pageurls[$i] = $this->url->show($id, $i, $catid, $rs['inputtime']);
                }
                $END_POS = strpos($content, '[/page]');
                if($END_POS !== false) {
                    if($CONTENT_POS>7) {
                        $content = '[page]'.$title.'[/page]'.$content;
                    }
                    if(preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
                        foreach($m[1] as $k=>$v) {
                            $p = $k+1;
                            $titles[$p]['title'] = strip_tags($v);
                            $titles[$p]['url'] = $pageurls[$p][0];
                        }
                    }
                }
                //当不存在 [/page]时，则使用下面分页
                $pages = content_pages($pagenumber,$this->page, $pageurls);
                //判断[page]出现的位置是否在第一位 
                if($CONTENT_POS<7) {
                    $content = $contents[$this->page];
                } else {
                    if ($this->page==1 && !empty($titles)) {
                        $content = $title.'[/page]'.$contents[$this->page-1];
                    } else {
                        $content = $contents[$this->page-1];
                    }
                }
                if($titles) {
                    list($title, $content) = explode('[/page]', $content);
                    $content = trim($content);
                    if(strpos($content,'</p>')===0) {
                        $content = '<p>'.$content;
                    }
                    if(stripos($content,'<p>')===0) {
                        $content = $content.'</p>';
                    }
                }
            }
            $data['title'] = $title;
            $data['content'] = $content;
        }
        require_once MODULE_CACHE.'content_output.class.php';
        $model_output = new \content_output($category['modelid'], $catid, $category);
        $data = $model_output->get($data);
        extract($data);
        $arrparentid = explode(',', $category['arrparentid']);
        $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
        //上一页
        $pre_page = M($tablename)->where("`catid` = '$catid' AND `id`<'$id' AND `status` = '99'")->find();
        //下一页
        $next_page = M($tablename)->where("`catid`= '$catid' AND `id`>'$id' AND `status` = '99'")->find();

        if(empty($pre_page)) {
            $pre_page = array('title'=> '第一页', 'thumb'=>IMG_PATH.'nopic_small.gif', 'url'=>'javascript:alert(\'第一页\');');
        }
        if(empty($next_page)) {
            $next_page = array('title'=> '最后一页', 'thumb'=>IMG_PATH.'nopic_small.gif', 'url'=>'javascript:alert(\'最后一页\');');
        }
        $this->pre_page = $pre_page;
        $this->next_page = $next_page;
        /* SEO标题 */
        $seo_keywords = '';
        if(!empty($keywords)) $seo_keywords = implode(',',$keywords);

        A('Api/Hits')->hits('c-'.$modelid.'-'.$id, $catid);

        $SEO = seo($catid, $title, $description, $seo_keywords);
    	include template($category['setting']['show_template']);
    }

    public function trial_help(){
        include template('trial_help');

    }


}