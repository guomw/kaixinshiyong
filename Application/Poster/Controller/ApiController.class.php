<?php
namespace Poster\Controller;
class ApiController extends \Common\Controller\BaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('poster/poster');
    }
    public function show($id = 0) {
        $id = (int) $id;
        if($id < 1) die;
        $r = $this->db->detail($id);
        if(!$r || $r['disabled'] == 1) die;
        if(($r['start_time'] && $r['start_time'] > NOW_TIME) || ($r['end_time'] && $r['end_time'] < NOW_TIME)) die;
        $r['setting'] = string2array($r['setting']);
        include MODULE_PATH.'show.html';
    }
}