<?php
global $_W, $_GPC;
$weid = $this->_weid;
$from_user = $this->_fromuser;
$setting = $this->getSetting();
$cur_nave = 'search';

$word = $setting['searchword'];
if ($word) {
    $words = explode(' ', $word);
}

$searchword = trim($_GPC['searchword']);
if ($searchword) {
    $strwhere = " AND title like '%" . $searchword . "%' ";
    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND is_show=1 and is_list=1  AND deleted=0 {$strwhere} ORDER BY
displayorder DESC,id DESC", array(':weid' => $weid));
} else {
    $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND is_hot=1 AND is_show=1 and is_list=1  AND deleted=0 ORDER BY displayorder
DESC,id DESC", array(':weid' => $weid));
}
if (empty($list) && $searchword != ''){
    message('暂无搜索结果!', $this->createMobileUrl('search', array()), "warning");
}
include $this->template($this->cur_tpl . '/search');