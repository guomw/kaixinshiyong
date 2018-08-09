<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Poster\Model;
class PosterModel extends \Think\Model {
    protected $_auto = array(
        array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
    );
}