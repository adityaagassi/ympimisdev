<?php

namespace App\Libraries;
use Exception;

class ActMLEasyIf {
    const NO_ERROR = 0;
    private $com = FALSE;
   function __construct($stn) {
        $com = new \COM('ActUtlType.ActMLUtlType');
        $com->ActLogicalStationNumber = new \VARIANT(intval($stn));
        $err = $com->Open();
        if ($err != ActMLEasyIf::NO_ERROR) {
            throw new Exception('PLC connection error');
        }
        $this->com = $com;
    }
    function __destruct() {
        if ($this->com != FALSE) $this->com->Close();
    }
    function read_data($addr, $len) {
        $v_buf = new \VARIANT(array_fill(0, $len, FALSE), VT_ARRAY | VT_VARIANT);
        $v_addr = new \VARIANT($addr);
        $v_len = new \VARIANT($len);
        $err = $this->com->ReadDeviceBlock2($v_addr, $v_len, $v_buf);
        if ($err != ActMLEasyIf::NO_ERROR) {
            throw new Exception('Error');
        }
        $data = array();
        foreach($v_buf as $v_val) {
            $data[] = $v_val;
        }
        return $data;
    }
}

?>