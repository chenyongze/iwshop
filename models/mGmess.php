<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class mGmess extends Model {

    /**
     * 编辑素材内容
     * @param int $msgId
     * @param type $title
     * @param type $content
     * @param type $desc
     * @param type $thumbMediaId
     * @param type $category
     */
    public function alterGmess($msgId, $title, $content, $desc, $catImg, $thumbMediaId = null, $category = 0) {
        if ($msgId > 0) {
            // 修改素材
            return $this->Dao->update(TABLE_GMESS)
                            ->set(array(
                                'title' => $title,
                                'content' => $content,
                                '`desc`' => $desc,
                                'catimg' => $catImg,
                                'thumb_media_id' => $thumbMediaId,
                                'category' => $category
                            ))->where('id', $msgId)->exec();
        } else {
            // 插入数据
            return $this->Dao->insert(TABLE_GMESS, 'title, content, `desc`, catimg, category, thumb_media_id')
                            ->values(array($title, $content, $desc, $catImg, $category, $thumbMediaId))->exec();
        }
    }

    /**
     * 获取素材分类
     * @param type $parent
     * @return type
     */
    public function getGmessCategory($parent = 0) {
        $SQL = "SELECT `cat_name` AS `name`,`id` AS `dataId` FROM `gmess_category` WHERE `parent` = $parent ORDER BY sort DESC;";
        $Lst = $this->Db->query($SQL, false);
        foreach ($Lst as &$l) {
            $l['dataId'] = intval($l['dataId']);
            $l['children'] = $this->getGmessCategory($l['dataId']);
            $l['open'] = 'true';
            $l['hasChildren'] = count($l['children']) > 0;
        }
        return $Lst;
    }

    /**
     * 获取素材列表
     * @global type $config
     * @return type
     */
    public function getGmessList() {
        global $config;
        $list = $this->Db->query("SELECT * FROM `gmess_page` WHERE `deleted` = 0 ORDER BY `id` DESC;");
        foreach ($list as &$l) {
            $l['href'] = "http://" . $this->server('HTTP_HOST') . "$config->shoproot?/Gmess/view/id=" . $l['id'];
        }
        return $list;
    }

    /**
     * 获取素材
     * @param type $id
     * @return type
     */
    public function getGmess($id) {
        return $this->Db->getOneRow("SELECT * FROM `gmess_page` WHERE `id` = $id;");
    }

    /**
     * 删除群发素材
     * @param type $id
     * @return type
     */
    public function deleteGmess($id) {
        $oldData = $this->getGmess($id);
        if (is_file(dirname(__FILE__) . '/../uploads/gmess/' . $oldData['catimg'])) {
            @unlink(dirname(__FILE__) . '/../uploads/gmess/' . $oldData['catimg']);
        }
        $ret = $this->Db->query("UPDATE `gmess_page` SET `deleted` = 1 WHERE `id` = $id;");
        if ($ret) {
            // 删除页面缓存
            $this->Smarty->clearAllCache();
            return true;
        }
        return false;
    }

}
