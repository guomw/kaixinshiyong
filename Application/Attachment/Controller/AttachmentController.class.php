<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Attachment\Controller;
use \Common\Controller\BaseController;
use \Common\Library\attachment;
Class AttachmentController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('attachment');
        $this->modules =  getcache('module','commons');
        $this->categorys = getcache('category', 'commons');
        $this->upload_url = __ROOT__.'/uploadfile/';
        $this->upload_path = SITE_PATH.'/uploadfile/';
        $this->imgext = array('jpg','gif','png','bmp','jpeg');
        $this->userid = session('userid') ? session('userid') : cookie('_userid');
        $this->isadmin = $this->admin_username = session('roleid') ? 1 : 0;
        $this->groupid = cookie('_groupid') ? cookie('_groupid') : -1;
    }

    /**
     * upload 普通上传
     * @author   <jason>
     */
    public function upload() {
        $grouplist = getcache('member_group', 'member');
        if($this->isadmin==0 && !$grouplist[$this->groupid]['allowattachment']) return false;
        $module = trim($_GET['module']);
        $catid = intval($_GET['catid']);
        $attachment = new attachment($module,$catid);
        $attachment->set_userid($this->userid);
        $allowext = C('upload_allowext');
        $a = $attachment->upload('upload',$allowext);
        if($a){
            $filepath = $attachment->uploadedfiles[0]['filepath'];
            $fn = intval($_GET['CKEditorFuncNum']);
            $this->upload_json($a[0],$filepath,$attachment->uploadedfiles[0]['filename']);
            $attachment->mkhtml($fn,$this->upload_url.$filepath,'');
        } else {
            echo $attachment->error();
            exit();
        }
    }

    /**
     * swfupload 上传
     * @author   <jason>
     * @return [type] [description]
     */
    public function swfupload() {
        $grouplist = getcache('member_group', 'member');
        if (isset($_POST['dosubmit'])) {

            $attachment = new attachment($_POST['module'],$_POST['catid']);
            $attachment->set_userid($_POST['userid']);
            $aids = $attachment->upload('Filedata',$_POST['filetype_post'],'','',array($_POST['thumb_width'],$_POST['thumb_height']),$_POST['watermark_enable']);
            $allowext = C('upload_allowext');
            $allowext_array = explode('|',$allowext);
            if(!in_array($attachment->uploadedfiles[0]['fileext'],$allowext_array)){
                exit('0');
            }

            if($aids[0]) {
                $filename= (strtolower(CHARSET) != 'utf-8') ? iconv('gbk', 'utf-8', $attachment->uploadedfiles[0]['filename']) : $attachment->uploadedfiles[0]['filename'];
                if($attachment->uploadedfiles[0]['isimage']) {
                    $output=$aids[0].','.$this->upload_url.$attachment->uploadedfiles[0]['filepath'].','.$attachment->uploadedfiles[0]['isimage'].','.$filename;
                    echo $output;

                    // 生成缩略图
                    $new_array = explode(".",$attachment->uploadedfiles[0]['filepath']);
                    $new_qian_name = $new_array[0];
                    $new_hou_name = $new_array[1];
                    $new_name = $this->upload_url.$new_qian_name.'_150'.'.'.$new_hou_name;
                    $this->img2thumb('.'.$this->upload_url.$attachment->uploadedfiles[0]['filepath'],'.'.$new_name,300,390);
                    //$this->img3thumb('150','.'.$this->upload_url.$attachment->uploadedfiles[0]['filepath'],'.'.$new_name);
                    // 把生成的头像缩略图移动到头像文件夹
                    $new_name = $this->upload_url.$new_qian_name.'250'.'.'.$new_hou_name;
                    $this->img2thumb('.'.$this->upload_url.$attachment->uploadedfiles[0]['filepath'],'.'.$new_name,400,490);

                } else {
                    $fileext = $attachment->uploadedfiles[0]['fileext'];
                    if($fileext == 'zip' || $fileext == 'rar') $fileext = 'rar';
                    elseif($fileext == 'doc' || $fileext == 'docx') $fileext = 'doc';
                    elseif($fileext == 'xls' || $fileext == 'xlsx') $fileext = 'xls';
                    elseif($fileext == 'ppt' || $fileext == 'pptx') $fileext = 'ppt';
                    elseif ($fileext == 'flv' || $fileext == 'swf' || $fileext == 'rm' || $fileext == 'rmvb') $fileext = 'flv';
                    else $fileext = 'do';
                    echo $aids[0].','.$this->upload_url.$attachment->uploadedfiles[0]['filepath'].','.$fileext.','.$filename;
                }           
                exit;
            } else {
                echo '0,'.$attachment->error();
                exit;
            }
        } else {
            $args = $_GET['args'];
            $authkey = $_GET['authkey'];
            extract(getswfinit($_GET['args']));
            include $this->admin_tpl('swfupload');
        }
    }

    /**
     * 生成缩略图
     * @author
     * @param string     源图绝对完整地址{带文件名及后缀名}
     * @param string     目标图绝对完整地址{带文件名及后缀名}
     * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
     * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
     * @param int        是否裁切{宽,高必须非0}
     * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
     * @return boolean
     */
    

     function img3thumb($width = 150,$src_img,$new_name){
        $image = new \Think\Image(); 
        $image->open($src_img);
        // 生成一个缩放后填充大小150*150的缩略图并保存为thumb.jpg
        $image->thumb($width,$width,\Think\Image::IMAGE_THUMB_FILLED)->save($new_name);

     }



    function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0){
        $image = new \Think\Image(); 
        $image->open($src_img);
        // 生成一个缩放后填充大小150*150的缩略图并保存为thumb.jpg
        if($height==0)
            $height=$width;
        $image->thumb($width,$height,\Think\Image::IMAGE_THUMB_FILLED)->save($dst_img);

    }


    /**
     * 设置upload上传的json格式cookie
     */
    private function upload_json($aid,$src,$filename) {
        $arr['aid'] = intval($aid);
        $arr['src'] = trim($src);
        $arr['filename'] = urlencode($filename);
        $json_str = json_encode($arr);
        $att_arr_exist = cookie('att_json');
        $att_arr_exist_tmp = explode('||', $att_arr_exist);
        if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
            return true;
        } else {
            $json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
            cookie('att_json',$json_str);
            return true;            
        }
    }

    /**
     * 图片裁剪
     * @return [type] [description]
     */
    public function crop_upload() {
        if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            $pic = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (isset($_GET['width']) && !empty($_GET['width'])) {
                $width = intval($_GET['width']);
            }
            if (isset($_GET['height']) && !empty($_GET['height'])) {
                $height = intval($_GET['height']);
            }
            if (isset($_GET['file']) && !empty($_GET['file'])) {
                $_GET['file'] = str_ireplace(array(';','php'),'',$_GET['file']);
                if(is_image($_GET['file'])== false || stripos($_GET['file'],'.php')!==false) exit();
                if (strpos($_GET['file'], $this->upload_url !== false)) {
                    $file = $_GET['file'];
                    $basename = basename($file);
                    if (strpos($basename, 'thumb_')!==false) {
                        $file_arr = explode('_', $basename);
                        $basename = array_pop($file_arr);
                    }
                    $fileext = strtolower(fileext($basename));
                    if (!in_array($fileext, array('jpg', 'gif', 'jpeg', 'png', 'bmp'))) exit();
                    $new_file = 'thumb_'.$width.'_'.$height.'_'.$basename;
                } else {                    
                    $module = trim($_GET['module']);
                    $catid = intval($_GET['catid']);
                    $attachment = new attachment($module, $catid);
                    $uploadedfile['filename'] = basename($_GET['file']); 
                    $uploadedfile['fileext'] = strtolower(fileext($_GET['file']));
                    if (in_array($uploadedfile['fileext'], array('jpg', 'gif', 'jpeg', 'png', 'bmp'))) {
                        $uploadedfile['isimage'] = 1;
                    }
                    $file_path = $this->upload_path.date('Y/md/');
                    helpers('dir');
                    dir_create($file_path);
                    $new_file = date('Ymdhis').rand(100, 999).'.'.$uploadedfile['fileext'];
                    $uploadedfile['filepath'] = date('Y/md/').$new_file;
                    $aid = $attachment->add($uploadedfile);
                }
                $filepath = date('Y/md/');
                file_put_contents($this->upload_path.$filepath.$new_file, $pic);
            } else {
                return false;
            }
            echo $this->upload_url.$filepath.$new_file;
            exit;
        }
    }

    /**
     * 加载图片库
     */
    public function album_load() {
        if(!$this->admin_username) return false;
        $where = $uploadtime = '';
        if($_GET['args']) extract(getswfinit($_GET['args']));
        if($_GET['dosubmit']){
            extract($_GET['info']);
            $where = '';
            $filename = safe_replace($filename);
            if($filename) $where = "AND `filename` LIKE '%$filename%' ";
            if($uploadtime) {
                $start_uploadtime = strtotime($uploadtime.' 00:00:00');
                $stop_uploadtime = strtotime($uploadtime.' 23:59:59');
                $where .= "AND `uploadtime` >= '$start_uploadtime' AND  `uploadtime` <= '$stop_uploadtime'";                
            }
            if($where) $where = substr($where, 3);
        }
        $page = $_GET['page'] ? $_GET['page'] : '1';
        $count = $this->db->where($where)->count();
        $infos = $this->db->where($where)->page($page, 8)->select();
        foreach($infos as $n=>$v){
            $ext = fileext($v['filepath']);
            if(in_array($ext,$this->imgext)) {
                $infos[$n]['src']=$this->upload_url.$v['filepath'];
                $infos[$n]['width']='80';
            } else {
                $infos[$n]['src']=file_icon($v['filepath']);
                $infos[$n]['width']='64';
            }
        }
        $pages = page($count, 8);
        $form = new \Common\Library\form();
        include $this->admin_tpl('album_list');
    }

    /**
     * 目录浏览模式添加图片
     */
    public function album_dir() {
        if(!$this->admin_username) return false;
        if($_GET['args']) extract(getswfinit($_GET['args']));
        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\','..'), '', trim($_GET['dir'])) : '';
        $filepath = $this->upload_path.$dir;
        $list = glob($filepath.'/'.'*');
        if(!empty($list)) rsort($list);
        $local = str_replace(array(PC_PATH, PHPCMS_PATH ,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('','',DIRECTORY_SEPARATOR), $filepath);
        $url = ($dir == '.' || $dir=='') ? $this->upload_url : $this->upload_url.str_replace('.', '', $dir).'/';
        $show_header = true;
        include $this->admin_tpl('album_dir');
    }
    
    /**
     * 删除附件
     */
    public function swfdelete() {
        $attachment = pc_base::load_sys_class('attachment');
        $att_del_arr = explode('|',$_GET['data']);
        foreach($att_del_arr as $n=>$att){
            if($att) $attachment->delete(array('aid'=>$att,'userid'=>$this->userid,'uploadip'=>ip()));
        }
    }


    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /**
     * 编辑器上传
     * @author 华强 <jason>
     */
    public function editor() {
        $config = C('ueditor');
        switch ($_GET['action']) {
            // 获取上传配置
            case 'config':
                echo json_encode($config);
                break;
            // 管理文件
            case 'listimage':
            case 'listfile':
                if($_GET['action'] == 'listfile') {
                    $allowFiles = $config['fileManagerAllowFiles'];
                    $listSize = $config['fileManagerListSize'];
                    $path = $config['fileManagerListPath'];
                } else {
                    $allowFiles = $config['imageManagerAllowFiles'];
                    $listSize = $config['imageManagerListSize'];
                    $path = $config['imageManagerListPath'];
                }
                $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
                $size = isset($_GET['size']) ? $_GET['size'] : $listSize;
                $start = isset($_GET['start']) ? $_GET['start'] : 0;
                $end = $start + $size;
                /* 获取文件列表 */
                $path = $this->upload_path . $path;
                $files = $this->getfiles($path, $allowFiles);
                $total = count($files);
                $state = ($total > 0) ? 'SUCCESS' : 'no match file';
                $lists = array();
                if($total > 0) {
                    for ($i = min($end, $total) - 1, $list = array(); $i < $total && $i >= 0 && $i >= $start; $i--){
                        $lists[] = $files[$i];
                    }
                }
                echo json_encode(array(
                    "state" => $state,
                    "list" => $lists,
                    "start" => $start,
                    "total" => $total
                ));
                break;
            //提交上传
            case 'uploadimage':
            case 'uploadscrawl':
            case 'uploadvideo':
            case 'uploadfile':
                $base64 = "upload";
                if($_GET['action'] == 'uploadimage') {
                    $params = array(
                        "pathFormat" => $config['imagePathFormat'],
                        "maxSize" => $config['imageMaxSize'],
                        "allowFiles" => $config['imageAllowFiles']
                    );
                    $fieldName = $config['imageFieldName'];                     
                } elseif($_GET['action'] == 'uploadscrawl') {
                    $params = array(
                        "pathFormat" => $config['scrawlPathFormat'],
                        "maxSize" => $config['scrawlMaxSize'],
                        "allowFiles" => $config['scrawlAllowFiles'],
                        "oriName" => "scrawl.png"
                    );
                    $fieldName = $config['scrawlFieldName'];
                    $base64 = "base64";
                } elseif ($_GET['action'] == 'uploadvideo') {
                    $params = array(
                        "pathFormat" => $config['videoPathFormat'],
                        "maxSize" => $config['videoMaxSize'],
                        "allowFiles" => $config['videoAllowFiles']
                    );
                    $fieldName = $config['videoFieldName'];
                } else {
                    $params = array(
                        "pathFormat" => $config['filePathFormat'],
                        "maxSize" => $config['fileMaxSize'],
                        "allowFiles" => $config['fileAllowFiles']
                    );
                    $fieldName = $config['fileFieldName'];
                }    
                $up = new \Common\Library\upload($fieldName, $params, $base64);
                echo json_encode($up->getFileInfo());
                break;
            case 'catchimage':
                /* 上传配置 */
                $param = array(
                    "pathFormat" => $config['catcherPathFormat'],
                    "maxSize" => $config['catcherMaxSize'],
                    "allowFiles" => $config['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $config['catcherFieldName'];
                $list = array();
                if (isset($_POST[$fieldName])) {
                    $source = $_POST[$fieldName];
                } else {
                    $source = $_GET[$fieldName];
                }
                foreach ($source as $imgUrl) {
                    $item = new \Common\Library\upload($imgUrl, $param, "remote");
                    $info = $item->getFileInfo();
                    array_push($list, array(
                        "state" => $info["state"],
                        "url" => $info["url"],
                        "source" => $imgUrl
                    ));
                }
                /* 返回抓取数据 */
                echo json_encode(array(
                    'state'=> count($list) ? 'SUCCESS':'ERROR',
                    'list'=> $list
                ));
                
                break;
            default:
                break;
        }
    }
    

}