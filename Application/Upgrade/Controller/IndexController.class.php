<?php
namespace Upgrade\Controller;
use \Upgrade\Library\PclZip;
class IndexController extends \Admin\Controller\InitController{
    private $upgrade_url = 'http://upgrade. .cn/';
    
    public function _initialize() {
        parent::_initialize();
    }

    public function index() {

        $authorization = new \Common\Library\authorization();
        if(!$authorization->check()) {
            return false;
            $this->error($authorization->getError());
        } 
        $current_version['system_version'] = C('SYSTEM_VERSION');
        $current_version['system_release'] = C('SYSTEM_RELEASE');
        $patch_base = $this->upgrade_url.C('SYSTEM_IDENTIFIER').'/patch/';
        $patch_str = @file_get_contents($patch_base);
        $pathlist = $allpathlist = array();
        // 获取所有升级包
        preg_match_all("/\"(patch_[\d]{8}_[\d]{8}+\.zip)\"/", $patch_str, $allpathlist);
        $allpathlist = $allpathlist[0];
        foreach ($allpathlist as $v) {
            preg_match("/patch_([\d]{8})_([\d]{8})+\.zip/", $v, $p);
            if(empty($p) || $p[1] < $current_version['system_release']) continue;
            $pathlist[] = $p[0];
        }
        if(submitcheck('dosubmit', 'GP')) {
            $param = I('param.');
            $tmp_k = (int) $param['tmp_k'];
			//创建缓存文件夹
			if(!file_exists(DATA_PATH.'caches_upgrade')) {
				@mkdir(DATA_PATH.'caches_upgrade');
			}
            $Dir = new \Common\Library\dir();
            $current_patch = $pathlist[$tmp_k];
            if($current_patch) {
                preg_match("/patch_([\d]{8})_([\d]{8})+\.zip/", $current_patch, $p);
                //远程压缩包地址
                $zip_url = $patch_base.$current_patch;
                //本地压缩包地址
                $local_path = DATA_PATH.'caches_upgrade'.DIRECTORY_SEPARATOR.$current_patch;
                //解压补丁文件地址
                $source_path = DATA_PATH.'caches_upgrade'.DIRECTORY_SEPARATOR.basename($current_patch,".zip");
                @file_put_contents($local_path, @file_get_contents($zip_url));
                //解压文件
                $archive = new PclZip($local_path);
                if($archive->extract(PCLZIP_OPT_PATH, $source_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                    die("Error : ".$archive->errorInfo(true));
                }
                $copy_from = $source_path;
                $copy_to = SITE_PATH;
                $Dir->copyDir($copy_from, $copy_to);
                
                // 执行升级SQL
                $upgrade_sqlfile = $copy_to.DIRECTORY_SEPARATOR.'upgrade.sql';
                if(file_exists($upgrade_sqlfile)) {
                    $query = @file_get_contents($upgrade_sqlfile);
                    if($query) sqlexecute($query);
                    @unlink($upgrade_sqlfile);
                }
                // 执行升级文件
                $upgrade_runfile = $copy_to.DIRECTORY_SEPARATOR.'upgrade.php';
                if(file_exists($upgrade_runfile)) {
                    require_cache($upgrade_runfile);
                    @unlink($upgrade_runfile);
                }
                // 升级版本号
                if(!file_exists($copy_from.DIRECTORY_SEPARATOR.'Application/Common/Conf/version.php')) {
                    $version_arr = array('SYSTEM_IDENTIFIER' => C('SYSTEM_IDENTIFIER'), 'SYSTEM_VERSION' => C('SYSTEM_VERSION'), 'SYSTEM_RELEASE' => $p[2], 'SYSTEM_FIXBUG' => str_pad((C('SYSTEM_FIXBUG') + 1),8, '0',STR_PAD_LEFT));
                    @file_put_contents(CONF_PATH.'version.php', '<?php return '.var_export($version_arr, true).';?>');
                }                
                @unlink($local_path);
                $Dir->delDir($source_path);
                // 删除缓存
                $Dir->del(CACHE_PATH);
                $Dir->del(DATA_PATH.'_fields');
                $Dir->del(LOG_PATH);
                //提示语
                $tmp_k = $tmp_k + 1;
                if(!empty($pathlist[$tmp_k])) {
                    $next_update = '<br />即将升级 '.basename($pathlist[$tmp_k],".zip");
                    $param['tmp_k'] = 0;
                } else {
                    $next_update = '<br/>若升级功能未生效，建议更新缓存再刷新重试。';
                    $param = array();
                }
                $this->success(basename($current_patch,".zip").' 升级成功！'.$next_update, U('index', $param));
            } else {
                $this->error('升级异常，请重试');
            }
        } else {
            $show_header = FALSE;
            include $this->admin_tpl('upgrade_index');   
        }
    }
    public function update(){
        $upgrade_runfile = SITE_PATH.'\upgrade.php';
        //var_dump($upgrade_runfile);exit();
        if(file_exists($upgrade_runfile)) {
            var_dump(111111);
            require_cache($upgrade_runfile);
            @unlink($upgrade_runfile);
        }
         // 执行升级SQL
        $upgrade_sqlfile = SITE_PATH.'\upgrade.sql';
        if(file_exists($upgrade_sqlfile)) {
            $query = @file_get_contents($upgrade_sqlfile);
            if($query) sqlexecute($query);
            @unlink($upgrade_sqlfile);
        }
        echo('升级成功');exit();
    }
}