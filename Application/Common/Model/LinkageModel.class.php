<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class LinkageModel extends Model {
	/*自动验证*/
	protected $_validate = array (
	);

	protected $_auto = array (
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['linkageid']) && is_numeric($data['linkageid'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '新增数据失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($linkageid, $field = TRUE) {
		$data = $this->field($field)->find($linkageid);
		if (!is_array($data)) {
			$this->error = '您查看的信息不存在';
			return false;
		}
		return $data;
	}

	/**
	 * 删除指定的菜单
	 * @param  mixed $linkageid 要删除的菜单ID
	 * @return bool
	 */
	public function _delete($linkageid) {
		$where = array();
		if (is_array($linkageid)) {
			$where['linkageid'] = array("IN", $linkageid);
		} else {
			$where['linkageid'] = (int) $linkageid;
		}
		$result = $this->where($where)->delete();
		return $result;
	}

	/**
	 * 获取指定ID的顶级菜单
	 * @param  int $id 当前菜单ID
	 * @return string||array
	 */
	public function get_topid($id, $field = null) {
		$row = $this->detail($id);
		if (!$row) {
			return FALSE;
		}
		if ($row['parentid'] > 0) {
			return $this->get_topid($row['parentid'], $field);
		}
		return (is_null($field)) ? $row : $row[$field];
	}

	/**
	 * 获取指定ID的所有上级列表
	 * @param  string $id    [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function get_parentids($id) {
		$row = $this->detail($id);
		if (!$row) {
			return FALSE;
		}
		$result .= $row['linkageid'].',';			
		if ($row['parentid'] > 0) {
			$result .= $this->get_parentids($row['parentid'], FALSE);
		}
		return rtrim($result, ',');
	}

	/**
	 * 获取指定ID的所有下级列表
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_sonids($id, $self = TRUE) {
		$where = array();
		$where['parentid'] = $id;
		$result = ($self === TRUE) ? $id.',' : '';
		$lists = $this->where($where)->field('linkageid, parentid')->select();
		foreach ($lists as $key => $value) {
			$result .= $value['linkageid'].',';
			$result .= $this->get_sonids($value['linkageid'], FALSE);
		}
		return rtrim($result, ",");
	}



    /**
     * 获取子菜单ID列表
     * @param $linkageid 联动菜单id
     * @param $linkageinfo
     */
    private function get_arrchildid($linkageid,$linkageinfo) {
        $arrchildid = $linkageid;
        if(is_array($linkageinfo)) {
            foreach($linkageinfo as $linkage) {
                if($linkage['parentid'] && $linkage['linkageid'] != $linkageid && $linkage['parentid']== $linkageid)    {
                    $arrchildid .= ','.$this->get_arrchildid($linkage['linkageid'],$linkageinfo);
    
                }
            }
        }
        return $arrchildid;
    }

    /**
     * 子菜单列表
     * @param unknown_type $keyid
     */
    private function submenulist($keyid=0) {
        $keyid = intval($keyid);
        $datas = array();
        $where = ($keyid > 0) ? array('keyid'=>$keyid) : '';
        $result = $this->where($where)->order('listorder ASC,linkageid ASC')->select();  
        if(is_array($result)) {
            foreach($result as $r) {
                $arrchildid = $r['arrchildid'] = $this->get_arrchildid($r['linkageid'],$result);                
                $child = $r['child'] =  is_numeric($arrchildid) ? 0 : 1;
                $this->update(array('linkageid'=>$r['linkageid'], 'child'=>$child,'arrchildid'=>$arrchildid));            
                $datas[$r['linkageid']] = $r;
            }
        }
        return $datas;
    }

	/* 更新缓存  */
	public function build_cache($linkageid = 0) {
        $linkageid = intval($linkageid);
        if ($linkageid < 1) return FALSE;
        $r = $this->getByLinkageid($linkageid);
        if ($r) {
            $info = array();
            $info['title'] = $r['name'];
            $info['style'] = $r['style'];
            $info['setting'] = string2array($r['setting']);
            $info['data'] = $this->submenulist($linkageid);
            setcache('linkage_'.$linkageid, $info, 'linkage');            
        }
        return TRUE;
	}
}