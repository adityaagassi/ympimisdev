<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $remark;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $remark)
    {
        $this->data = $data;
        $this->remark = $remark;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->remark == 'shipment'){
            return $this->from('ympimis@gmail.com')->subject('MIS Shipment Reminder (情報管理システムの出荷通知)')->view('mails.shipment');
        }
        if($this->remark == 'overtime'){
            return $this->from('ympimis@gmail.com')->subject('MIS Overtime Information (情報管理システムの残業情報)')->view('mails.overtime');
        }
        if($this->remark == 'stuffing'){
            return $this->from('ympimis@gmail.com')->subject('MIS Stuffing Information (情報管理システムの荷積み情報)')->view('mails.stuffing');
        }
        if($this->remark == 'min_queue'){
            return $this->from('ympimis@gmail.com')->subject('MIS Kanban Queue Information (情報管理システムのかんばん待ちの情報)')->view('mails.min_queue');
        }
        if($this->remark == 'middle_kanban'){
            return $this->from('ympimis@gmail.com')->subject('MIS Kanban WIP Information (情報管理システムのかんばん待ちの情報)')->view('mails.middle_kanban');
        }
        if($this->remark == 'duobleserialnumber'){
            return $this->from('ympimis@gmail.com')->subject('MIS Double Serial Number Information (情報管理システムの二重製造番号の情報)')->view('mails.duobleserialnumber');
        }
        if($this->remark == 'confirmation_overtime'){
            return $this->from('ympimis@gmail.com')->subject('MIS Unconfirmed Overtime (情報管理システムの未確認残業)')->view('mails.confirmation_overtime');
        }
    }
}
