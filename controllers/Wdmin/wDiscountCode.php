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
class wDiscountCode extends Controller {

    const TPL = './views/wdminpage/';

    private $table = 'wshop_discountcodes';
    private $table2 = 'wshop_discountcode';

    /**
     * 
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
        $this->Session->start();
        $loginKey = $this->Session->get('loginKey');
        if (!$loginKey || empty($loginKey)) {
            $this->redirect('?/Wdmin/logOut');
        }
    }

    /**
     * 批量上传优惠码
     */
    public function upload($Q) {
        $qid = $Q->id;
        $this->Load->model('ImageUploader');
        if (!empty($_FILES)) {
            $tempFile = $_FILES['jUploaderFile']['tmp_name'];
            $files = fopen($tempFile, 'r');
            while (!feof($files)) {
                $code = str_replace(PHP_EOL, '', fgets($files));
                $this->Dao->insert($this->table, 'codes,qid')->values(array(trim($code), $qid))->exec();
            }
            fclose($files);
            $this->echoMsg(0);
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 编辑优惠活动
     * @param type $Q
     */
    public function alterCodes($Q) {
        $id = $this->post('id') ? $this->post('id') : false;
        $keywords = $this->post('keywords');
        $discount = $this->post('discount');
        $template = $this->post('template');
        $fg = false;
        if ($id > 0) {
            // alter
            $fg = $this->Dao->update($this->table2)->set(array('keywords' => $keywords, 'code_discount' => $discount, 'template' => $template))->where('id', $id)->exec();
        }
        if ($id < 0) {
            // delete
            $fg = $this->Dao->delete()->from($this->table2)->where('id', abs($id))->exec();
            if ($fg) {
                $fg = $this->Dao->delete()->from($this->table)->where('qid', abs($id))->exec();
            }
        }
        if (!$id) {
            // create
            $fg = $this->Dao->insert($this->table2, 'keywords, code_discount, `template`')->values(array($keywords, $discount, $template))->exec();
        }
        $this->echoMsg($fg ? 0 : -1);
        $fg && $this->Smarty->clearCache('discount_code_list', 'discount_code_list');
    }

    /**
     * 编辑优惠码
     * @param type $Q
     */
    public function alterCode($Q) {
        $id = $this->post('id') ? $this->post('id') : false;
        $qid = $this->post('qid') ? $this->post('qid') : false;
        $codes = $this->post('codes');
        if (!$codes && $id > 0) {
            $this->echoMsg(-1);
        } else {
            $fg = false;
            if ($id > 0) {
                // alter
                $fg = $this->Dao->update($this->table)->set(array('codes' => $codes))->where('id', $id)->exec();
            }
            if ($id < 0) {
                // delete
                $fg = $this->Dao->delete()->from($this->table)->where('id', abs($id))->exec();
            }
            if (!$id) {
                // create
                $fg = $this->Dao->insert($this->table, 'codes, qid')->values(array($codes, $qid))->exec();
            }
            $this->echoMsg($fg ? 0 : -1);
            $fg && $this->Smarty->clearCache('discount_code_list', 'discount_code_list');
        }
    }

}
