<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Oauth\Library;
abstract class OauthInterface {
    protected $config = array();
    protected $error = '';
    
    public function __construct($config) {
        $this->config = $config;
        $this->getInstance();
    }
    abstract public function getInstance();
    /* 登录 */
    abstract public function login();
    
    public function getError() {
        return $this->error;
    }
}
