<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Controller;
use \Admin\Controller\InitController;
use Common\Library\form;
Class CategoryController extends InitController {
    /**
     * 初始化
     * @author   <jason>
     */
    public function _initialize() {
    	parent::_initialize();
        $this->db = model('product_category');
        $this->model_db = D('Model');
    }

    /**
     * 管理分类
     * @author   <jason>
     */
    public function init() {
        $tree = new \Common\Library\tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $categorys = array();
        //读取缓存
        $result = getcache('product_category', 'commons');
        $models = getcache('model', 'commons');
        $category_items = array();
        foreach ($models as $modelid=>$model) {
            $category_items[$modelid] = getcache('category_items_'.$modelid,'commons');
        }

        $show_detail = count($result) < 500 ? 1 : 0;
        $parentid = $_GET['parentid'] ? intval($_GET['parentid']) : 0;
        $html_root = C('HTML_ROOT');
        $types = array(0 => '内部分类',1 => '<font color="blue">单网页</font>',2 => '<font color="red">外部链接</font>');
        if(!empty($result)) {
            foreach($result as $r) {
                $r['str_manage'] = '';
                if(!$show_detail) {
                    if($r['parentid']!=$parentid) continue;
                    $r['parentid'] = 0;
                    $r['str_manage'] .= '<a href="'.U('Category/init', array('parentid' => $r['catid'], 'menuid' => MENUID, 'type' => $r['menuid'], 'fromhash' => session('FROMHASH'))).'">管理子分类</a> | ';
                }
                $r['str_manage'] .= '<a href="'.U('Category/add', array('parentid' => $r['catid'], 'menuid' => MENUID, 'type' => $r['menuid'], 'fromhash' => session('FROMHASH'))).'">添加下级分类</a> | ';
                
                $r['str_manage'] .= '<a href="'.U('Category/edit', array('catid' => $r['catid'], 'menuid' => MENUID, 'type' => $r['menuid'], 'fromhash' => session('FROMHASH'))).'">编辑</a> | <a href="javascript:confirmurl(\''.U('Category/delete', array('catid' => $r['catid'], 'menuid' => MENUID, 'type' => $r['menuid'], 'fromhash' => session('FROMHASH'))).'\',\'您确定要删除这个分类吗？\')">删除</a>';
                $r['display_icon'] = $r['ismenu'] ? '' : ' <img src ="'.__ROOT__.'/static/images/icon/gear_disable.png" title="'.L('not_display_in_menu').'">';
                if($r['type'] || $r['child']) {
                    $r['items'] = '-';
                } else {
                    $r['items'] = $category_items[$r['modelid']][$r['catid']];
                }
                $r['help'] = '';
                $setting = string2array($r['setting']);
                if($r['url']) {
                    if(preg_match('/^(http|https):\/\//', $r['url'])) {
                        $catdir = $r['catdir'];
                        $prefix = $r['sethtml'] ? '' : $html_root;
                        if($this->siteid==1) {
                            $catdir = $prefix.'/'.$r['parentdir'].$catdir;
                        } else {
                            $catdir = $prefix.'/'.$sitelist[$this->siteid]['dirname'].$html_root.'/'.$catdir;
                        }
                    } else {
                        $r['url'] = substr($sitelist[$this->siteid]['domain'],0,-1).$r['url'];
                    }
                    $r['url'] = "<a href='$r[url]' target='_blank'>访问</a>";
                } else {
                    $r['url'] = "<a href='".U('public_cache')."'><font color='red'>更新缓存</font></a>";
                }
                if($r['isrecommend'] == 1){
                	$r['isrecommend'] = '是';
                }else{
                	$r['isrecommend'] = '否';
                }
                $categorys[$r['catid']] = $r;
            }
        }
        $str  = "<tr>
                    <td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
                    <td align='center'>\$id</td>
                    <td >\$spacer\$catname\$display_icon</td>
                    <td align='center'>\$items</td>
                    <td align='center'>\$url</td>
                     <td align='center' >\$isrecommend</td>
                    <td align='center' >\$str_manage</td>
                </tr>";
        $tree->init($categorys);
        $categorys = $tree->get_tree(0, $str);
        include $this->admin_tpl('category_manage');
    }

    /**
     * 添加分类
     * @author   <jason>
     */
    public function add($type = 0) {
        $type = intval($type);
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['modelid'] = (int) $info['modelid'];
            $info['parentid'] =(int) $info['parentid'];
            $info['type'] = 0;
            $info['setting'] = (array) $_POST['setting'];
            $info['setting']['category_ruleid'] = (int) ($info['setting']['ishtml'] == 1) ? $_POST['category_html_ruleid'] : $_POST['category_php_ruleid'];
            $info['setting']['show_ruleid'] = (int) ($info['setting']['content_ishtml'] == 1) ? $_POST['show_html_ruleid'] : $_POST['show_php_ruleid'];
            $result = $this->db->add($info);
            if ($result === FALSE) {
                $this->error($this->db->getError());
            }
            $this->public_cache();
             $this->success('操作成功');
        } else {
            $parentid = I('parentid', '0', 'intval');
            $categorys = getcache('product_category', 'commons');
            $modelid = 0;
            if ($parentid > 0) {
                $modelid = $categorys[$parentid]['modelid'];
                $type = $categorys[$parentid]['type'];
            }
            /* 加载可用风格 */
            $template_list = template_list();
            foreach ($template_list as $k=>$v) {
                $template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($template_list[$k]);
            }
            /* 模板赋值及渲染 */
            switch ($type) {
                case '1':
                    $_tpl = 'category_page_add';
                    break;
                case '2':
                    $_tpl = 'category_link';
                    break;
                default:
                    $_tpl = 'category_add';
                    break;
            }
            $form = new form();
            include $this->admin_tpl($_tpl);
        }
    }

    /**
     * 分类编辑
     * @author   <jason>
     */
    public function edit($catid = 0) {
        $catid = (int) $catid;
        $data = $this->db->detail($catid);
        if ($data === FALSE) {
            $this->error($this->db->getError());
        }
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['catid'] = $catid;
            $info['setting'] = $_POST['setting'];
            $info['setting']['category_ruleid'] = (int) ($info['setting']['ishtml'] == 1) ? $_POST['category_html_ruleid'] : $_POST['category_php_ruleid'];
            $info['setting']['show_ruleid'] = (int) ($info['setting']['content_ishtml'] == 1) ? $_POST['show_html_ruleid'] : $_POST['show_php_ruleid'];
            $result = $this->db->where('catid='.$catid)->save($info);
            if ($result === FALSE) {
                $this->error($this->db->getError());
            }
            $this->public_cache();
             $this->success('操作成功');            
        } else {
            $data['setting'] = string2array($data['setting']);
            /* 加载可用风格 */
            $template_list = template_list();
            foreach ($template_list as $k=>$v) {
                $template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($template_list[$k]);
            }
            /* 加载分类模板 */
            $tpls = array('category_edit', 'category_page_edit', 'category_link');
            $show_dialog = $show_validator = true;
            $form = new form();
            $_tpl = $tpls[$data['type']];
            extract($data);
            include $this->admin_tpl($_tpl);
        }  
    }

    /**
     * 删除分类
     * @author   <jason>
     */
    public function delete($catid = 0) {
        $categorys = getcache('category', 'commons');
        $models = getcache('model', 'commons');
        $modelid = $categorys[$catid]['modelid'];
        $this->delete_child($catid, $modelid);
        $this->db->where(array('catid'=>$catid))->delete();
        if ($modelid != 0) {
            $this->delete_document($catid, $modelid);
        }
        $this->public_cache();
        $this->success('操作成功');
    }

    /**
     * 递归删除子分类
     * @return [type]           [description]
     */
    private function delete_child($catid = 0, $modelid = 0) {
        $catid = (int) $catid;
        if ($catid < 1) return FALSE;
        $r = $this->db->where(array('parentid'=>$catid))->find();
        if($r) {
            $this->delete_child($r['catid']);
            $this->db->where(array('catid'=>$r['catid']))->delete();
            if ($modelid != 0) {
                $this->delete_document($catid, $modelid);
            }
        }
        return TRUE;
    }

    /* 删除分类文档 */
    private function delete_document($catid = 0, $modelid = 0) {
        $document_model = D('Document/Document');
        $document_model->set_model($modelid);
        $result = $document_model->delete_content(0, $catid, $modelid);
        return TRUE;
    }

    public function remove($catid = 0) {
        $catid = (int) $catid;
        $this->categorys = getcache('category', 'commons');
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $categorys = array();
        $document_model = D('Document');
        if (submitcheck('dosubmit')) {
            $info = $_POST;
            if (!$info['fromid']) {
                $this->error('请选择来源分类');
            }
            if (!$info['tocatid']) {
                $this->error('请选择目标分类');
            }            
            $fromid = (array) $info['fromid'];
            $tocatid = (int) $info['tocatid'];
            $modelid = $this->categorys[$tocatid]['modelid'];
            $document_model->set_model($modelid);
            $document_model->remove($fromid, $tocatid);
            $this->success('操作成功');
        } else {
            $tree = new \Common\Library\tree();
            $tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;';
            foreach($this->categorys as $cid=>$r) {
                if ($r['type'] != 0) continue;
                if($modelid && $modelid != $r['modelid']) continue;
                $r['disabled'] = $r['child'] ? 'disabled' : '';
                $r['selected'] = $cid == $catid ? 'selected' : '';
                $categorys[$cid] = $r;
            }
            $str  = "<option value='\$catid' \$disabled>\$spacer \$catname</option>";
            $tree->init($categorys);
            $string .= $tree->get_tree(0, $str);                   
            $str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
            $source_string = '';
            $tree->init($categorys);
            $source_string .= $tree->get_tree(0, $str);
            $show_header = FALSE;
            include $this->admin_tpl('category_remove');
        }
    }

    /**
     * 切换模型风格
     * @author   <jason>
     */
    public function public_change_tpl($modelid = 0) {
        $models = getcache('model', 'commons');
        $modelid = intval($modelid);
        if($modelid) {
            $form = new form();
            $style = $models[$modelid]['default_style'];
            $models[$modelid]['setting'] = string2array($models[$modelid]['setting']);
            $category_template = $models[$modelid]['setting']['category_template'];
            $list_template = $models[$modelid]['setting']['list_template'];
            $show_template = $models[$modelid]['setting']['show_template'];
            $html = array(
                'template_list'     => $style, 
                'category_template' => $form::select_template($style, 'document',$category_template,'name="setting[category_template]"','category'), 
                'list_template'     => $form::select_template($style, 'document',$list_template,'name="setting[list_template]"','list'),
                'show_template'     => $form::select_template($style, 'document',$show_template,'name="setting[show_template]"','show')
            );
            echo json_encode($html);
        }
    }

    /**
     * 加载模板
     * @author   <jason>
     */
    public function public_tpl_file_list() {
        $form = new form();
        $style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
        $catid = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : 0;
        $batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
        if ($catid) {
            $cat = getcache('category','commons');
            $cat = $cat[$catid];
            $cat['setting'] = string2array($cat['setting']);
        }
        if($_GET['type']==1) {
            $html = array('page_template'=>$form::select_template($style, 'document',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
        } else {
            $html = array('category_template'=> $form::select_template($style, 'document',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
                'list_template'=>$form::select_template($style, 'document',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
                'show_template'=>$form::select_template($style, 'document',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
            );
        }
        if ($_GET['module']) {
            unset($html);
            if ($_GET['templates']) {
                $templates = explode('|', $_GET['templates']);
                if ($_GET['id']) $id = explode('|', $_GET['id']);
                if (is_array($templates)) {
                    foreach ($templates as $k => $tem) {
                        $t = $tem.'_template';
                        if ($id[$k]=='') $id[$k] = $tem;
                        $html[$t] = $form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
                    }
                }
            }
            
        }
        if (CHARSET == 'gbk') {
            $html = array_iconv($html, 'gbk', 'utf-8');
        }
        echo json_encode($html);
        exit();
        $style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
        $style = I('style', '');
        $catid = I('catid', 0);
        $batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
        if ($catid) {
            $cat = F('Category');
            $cat = $cat[$catid];
            $cat['setting'] = string2array($cat['setting']);
        }
        $form = new form();
        if($_GET['type']==1) {
            $html = array(
                'page_template'=> $form::select_template($style, 'document',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
        } else {
            $html = array(
                'category_template'=> $form::select_template($style, 'document',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
                'list_template'=> $form::select_template($style, 'document',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
                'show_template'=> $form::select_template($style, 'document',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
            );
        }
        if ($_GET['module']) {
            unset($html);
            if ($_GET['templates']) {
                $templates = explode('|', $_GET['templates']);
                if ($_GET['id']) $id = explode('|', $_GET['id']);
                if (is_array($templates)) {
                    foreach ($templates as $k => $tem) {
                        $t = $tem.'_template';
                        if ($id[$k]=='') $id[$k] = $tem;
                        $html[$t] = form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
                    }
                }
            }   
        }
        echo json_encode($html);
    }

    /**
     * 检查目录
     * @author   <jason>
     */
    public function public_check_catdir($catdir = null) {
        echo "1";
        exit();
        if (empty($catdir)) {
            $this->error('英文目录不能为空');
        }
        $parentid = I('parentid', 0);
        $map = array();
        $map['parentid'] = $parentid;
        $r = $this->db->where($map)->select();
        if ($r['catdir'] == $catdir) {
            // $this->error('目录已存在');
        }
        $this->success('操作成功');
    }

    /**
     * 更新分类缓存
     * 获取分类关系，生成URL地址
     * @author   <jason>
     * @return [type] [description]
     */
    public function public_cache() {
        $this->public_repair();
        $this->db->build_cache();
        $this->success('操作成功');
    }

    /**
     * 统计分类数据
     * @author   <jason>
     */
    public function public_count() {
        $this->document = D('Document/Document');
        $result = getcache('category', 'commons');
        foreach ($result as $r) {
            if ($r['type'] == 0) {
                $modelid = $r['modelid'];
                $this->document->set_model($modelid);
                $number = M($this->document->tableName)->where(array('catid'=>$r['catid']))->count();
                $this->db->save(array('catid'=>$r['catid'], 'items'=>$number), FALSE);
            }
        }
        $this->db->build_cache();
        $this->success('操作成功');
    }

    /**
     * [私有]修复分类数据
     * @return [type] [description]
     */
    private function public_repair() {
        $category = array();
        $category = $this->db->field()->order('listorder ASC, catid ASC')->select();
        if(!is_array($category)) {
            return FALSE;
        }
        $this->get_categorys($category);
        foreach($this->categorys as $catid => $cat) {
            if($cat['type'] == 2) continue; //忽略外部链接
            $setting = string2array($cat['setting']);
            $this->categorys[$catid]['arrparentid'] = $arrparentid = $this->get_arrparentid($catid);
            $this->categorys[$catid]['arrchildid'] = $arrchildid = $this->get_arrchildid($catid);
            $this->categorys[$catid]['child'] = $child = is_numeric($arrchildid) ? 0 : 1;
            // 更新分类关系
            if($cat['arrparentid'] != $arrparentid || $cat['arrchildid']!= $arrchildid || $cat['child']!=$child) {
                $result = $this->db->save(array('catid'=>$catid, 'arrparentid'=>$arrparentid,'arrchildid' => $arrchildid,'child'=>$child));
            }
            /*获取上级目录*/
            $parentdir = $this->get_parentdir($catid);
            $catname = $cat['catname'];
            $letter = strtolower(getPinyin($catname));
            $listorder = $cat['listorder'] ? $cat['listorder'] : $catid;
            $this->sethtml = $setting['create_to_html_root'];
            //检查是否生成到根目录
            $this->get_sethtml($catid);
            $this->categorys[$catid]['sethtml'] = $sethtml = $this->sethtml ? 1 : 0;            
            if($setting['ishtml']) {
            //生成静态时
                $url = $this->update_url($catid);
                if(!preg_match('/^(http|https):\/\//i', $url)) {
                    $url = $sethtml ? '/'.$url : $html_root.'/'.$url;
                }
            } else {
            //不生成静态时
                $url = $this->update_url($catid);
                $url = __ROOT__.'/'.$url;
            }
            if($cat['url'] != $url) $this->db->save(array('catid'=>$catid, 'url'=>$url));            
            if($cat['parentdir'] != $parentdir || $cat['sethtml'] != $sethtml || $cat['letter'] != $letter || $cat['listorder'] != $listorder) $this->db->save(array('catid'=>$catid, 'parentdir'=>$parentdir,'sethtml'=>$sethtml,'letter'=>$letter,'listorder'=>$listorder));
        }
        return TRUE;
    } 

    /**
     * 找出子目录列表
     * @param array $categorys
     */
    private function get_categorys($categorys = array()) {
        $this->categorys = array();        
        if (is_array($categorys) && !empty($categorys)) {
            foreach ($categorys as $c) {
                $result[$c['catid']] = $c;
            }
        }
        $this->categorys = $result;
        return true;
    }

    /**
     * 获取父分类路径
     * @param  $catid
     */
    private function get_parentdir($catid) {
        if($this->categorys[$catid]['parentid']==0) return '';
        $r = $this->categorys[$catid];
        $setting = string2array($r['setting']);
        $url = $r['url'];
        $arrparentid = $r['arrparentid'];
        unset($r);
        if (strpos($url, '://')===false) {
            if ($setting['creat_to_html_root']) {
                return '';
            } else {
                $arrparentid = explode(',', $arrparentid);
                $arrcatdir = array();
                foreach($arrparentid as $id) {
                    if($id==0) continue;
                    $arrcatdir[] = $this->categorys[$id]['catdir'];
                }
                return implode('/', $arrcatdir).'/';
            }
        } else {
            if ($setting['create_to_html_root']) {
                if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $url, $matches)) {
                    $url = $matches[0].'/';
                    $rs = $this->db->get_one(array('url'=>$url), '`parentdir`,`catid`');
                    if ($catid == $rs['catid']) return '';
                    else return $rs['parentdir'];
                } else {
                    return '';
                }
            } else {
                $arrparentid = explode(',', $arrparentid);
                $arrcatdir = array();
                krsort($arrparentid);
                foreach ($arrparentid as $id) {
                    if ($id==0) continue;
                    $arrcatdir[] = $this->categorys[$id]['catdir'];
                    if ($this->categorys[$id]['parentdir'] == '') break;
                }
                krsort($arrcatdir);
                return implode('/', $arrcatdir).'/';
            }
        }
    }  

    /**
     * 获取父分类ID列表
     * @author   <jason>
     * @param  int  $catid       分类ID
     * @param  int  $arrparentid 父目录ID
     * @param  integer $n        查找的层次
     */
    private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
        if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
        $parentid = $this->categorys[$catid]['parentid'];
        $arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
        if($parentid) {
            $arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
        } else {
            $this->categorys[$catid]['arrparentid'] = $arrparentid;
        }
        $parentid = $this->categorys[$catid]['parentid'];
        return $arrparentid;
    }

    /**
     * 获取子分类ID列表
     * @author   <jason>
     * @param  int $catid 分类ID
     */
    private function get_arrchildid($catid) {
        $arrchildid = $catid;
        if(is_array($this->categorys)) {
            foreach($this->categorys as $id => $cat) {
                if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
                    $arrchildid .= ','.$this->get_arrchildid($id);
                }
            }
        }
        return $arrchildid;
    }

    /**
     * 获取父分类是否生成到根目录
     */
    private function get_sethtml($catid) {
        foreach($this->categorys as $id => $cat) {
            if($catid==$id) {
                $parentid = $cat['parentid'];
                if($this->categorys[$parentid]['sethtml']) {
                    $this->sethtml = 1;
                }
                if($parentid) {
                    $this->get_sethtml($parentid);
                }
            }
        }
    }

    /**
    * 更新分类链接地址
    */
    private function update_url($catid) {
        $catid = intval($catid);
        if (!$catid) return false;
        $url = new \Common\Library\url(); //调用URL实例
        return $url->category_url($catid);
    }

    /**
     * 检查分类权限
     * @param $action 动作
     * @param $roleid 角色
     * @param $is_admin 是否为管理组
     */
    private function check_category_priv($action,$roleid,$is_admin = 1) {
        $checked = '';
        foreach ($this->privs as $priv) {
            if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
        }
        return $checked;
    }

    public function listorder() {
        if (submitcheck('dosubmit')) {
            $listorders = $_POST['listorders'];
            if (!is_array($listorders) || empty($listorders)) {
                $this->error('参数错误');
            }
            foreach ($listorders as $key => $value) {
                $this->db->save(array('catid' => $key, 'listorder' => $value));
            }
            $this->public_cache();
            $this->success('操作成功');
        } else {
            $this->error('请勿非法访问');
        }
    }

    /**
     * 无刷新查询分类
     * @author   <jason>
     */
    public function public_ajax_search($catname = '') {
        $catname = trim($catname);
        $result = array('status' => 0, 'info' => '');
        if ($catname) {
            $map = array();
            $map['catname|letter'] = array('LIKE', '%'.$catname.'%');
            $data = $this->db->field('catid, type, catname')->where($map)->select();
            if (is_array($data) && !empty($data)) {
                foreach ($data as $key => $value) {
                    $_action = ($value['type'] == 0) ? 'manage' : 'add';
                    $data[$key]['href'] = U('Document/Document/'.$_action, array('catid' => $value['catid'], 'fromhash' => session('fromhash')));
                }             
                $result['status'] = 1;
                $result['info'] = $data;
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * 按模型ID加载分类
     * @author   <jason>
     */
    public function public_category_load($modelid = 0) {
        $form = new \Common\Library\form;
        $modelid = (int) $modelid;
        $category = $form::select_category(0, 'name="info[catid]"', '请选择分类', $modelid);
        echo $category;
        exit();
    }

}