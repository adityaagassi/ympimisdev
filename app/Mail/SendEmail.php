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
        if($this->remark == 'cpar'){
            return $this->from('ympimis@gmail.com')->subject('Corrective and Preventive Action Request (CPAR) (是正防止処置要求)')->view('mails.cpar');
        }
        if($this->remark == 'rejectcpar'){
            return $this->from('ympimis@gmail.com')->subject('Penolakan Corrective and Preventive Action Request (CPAR) (是正防止処置要求)')->view('mails.rejectcpar');
        }
        if($this->remark == 'car'){
            return $this->from('ympimis@gmail.com')->subject('Corrective Action Report (CAR) (是正処置対策)')->view('mails.car');
        }
        if($this->remark == 'user_document'){
            return $this->from('ympimis@gmail.com')->subject('Users Documents Reminder (??)')->view('mails.user_document');
        }
        if($this->remark == 'audit'){
            return $this->from('ympimis@gmail.com')->subject('Production Audit Report (??)')->view('mails.audit');
        }
        if($this->remark == 'sampling_check'){
            return $this->from('ympimis@gmail.com')->subject('Sampling Check Report (??)')->view('mails.sampling_check');
        }
        if($this->remark == 'laporan_aktivitas'){
            return $this->from('ympimis@gmail.com')->subject('Laporan Aktivitas Audit (??)')->view('mails.laporan_aktivitas');
        }
        if($this->remark == 'training'){
            return $this->from('ympimis@gmail.com')->subject('Training Report (??)')->view('mails.training');
        }
        if($this->remark == 'interview'){
            return $this->from('ympimis@gmail.com')->subject('Interview Yubisashikosou Report (??)')->view('mails.interview');
        }
        if($this->remark == 'daily_check'){
            return $this->from('ympimis@gmail.com')->subject('Daily Check FG (??)')->view('mails.daily_check');
        }
        if($this->remark == 'labeling'){
            return $this->from('ympimis@gmail.com')->subject('Labeling Safety Sign (??)')->view('mails.labeling');
        }
        if($this->remark == 'audit_process'){
            return $this->from('ympimis@gmail.com')->subject('Audit Process (??)')->view('mails.audit_process');
        }
        if($this->remark == 'first_product_audit'){
            return $this->from('ympimis@gmail.com')->subject('Audit Cek Produk Pertama Monthly Evidence (??)')->view('mails.first_product_audit');
        }
        if($this->remark == 'first_product_audit_daily'){
            return $this->from('ympimis@gmail.com')->subject('Audit Cek Produk Pertama Daily Evidence (??)')->view('mails.first_product_audit_daily');
        }
    }
}
