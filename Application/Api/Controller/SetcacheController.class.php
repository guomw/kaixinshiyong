<?php 
namespace Api\Controller;
use \Common\Controller\BaseController;



/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
class SetcacheController extends BaseController {
    public function _initialize() {
        parent::_initialize(); 
        
    }

    public function index(){
         $info = array();
         $info['now_time'] = NOW_TIME;
         F('cache', $info);
        runhook('system_init','');

    }

    public function cache(){
         $data = F('cache');  
         if ($data == false) {
             $info = array();
             $info['now_time'] = NOW_TIME;
             F('cache', $info);
         }

        if (NOE_TIME > $data['now_time']+60) {
             $info = array();
             $info['now_time'] = NOW_TIME;
             F('cache', $info);
            runhook('system_init','');


         }
      
   
 }




  


}                