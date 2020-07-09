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
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Shipment Reminder (情報管理システムの出荷通知)')->view('mails.shipment');
        }
        if($this->remark == 'overtime'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Overtime Information (情報管理システムの残業情報)')->view('mails.overtime');
        }
        if($this->remark == 'stuffing'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Stuffing Information (情報管理システムの荷積み情報)')->view('mails.stuffing');
        }
        if($this->remark == 'min_queue'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Kanban Queue Information (情報管理システムのかんばん待ちの情報)')->view('mails.min_queue');
        }
        if($this->remark == 'middle_kanban'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Kanban WIP Information (情報管理システムのかんばん待ちの情報)')->view('mails.middle_kanban');
        }
        if($this->remark == 'duobleserialnumber'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Double Serial Number Information (情報管理システムの二重製造番号の情報)')->view('mails.duobleserialnumber');
        }
        if($this->remark == 'confirmation_overtime'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Unconfirmed Overtime (情報管理システムの未確認残業)')->view('mails.confirmation_overtime');
        }
        if($this->remark == 'cpar'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('CPAR '.$this->data[0]->judul_komplain.' (是正防止処置要求)')->view('mails.cpar');
        }
        if($this->remark == 'rejectcpar'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Penolakan Corrective and Preventive Action Request (CPAR) (是正防止処置要求)')->view('mails.rejectcpar');
        }
        if($this->remark == 'car'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('CAR '.$this->data[0]->judul_komplain.' (Corrective Action Report) (是正処置対策)')->view('mails.car');
        }
        if($this->remark == 'rejectcar'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Penolakan Corrective Action Report (CAR) (是正処置対策)')->view('mails.rejectcar');
        }
        if($this->remark == 'user_document'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Users Documents Reminder (ユーザ資料関連の催促メール)')->view('mails.user_document');
        }
        if($this->remark == 'audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Production Audit Report (生産監査報告)')->view('mails.audit');
        }
        if($this->remark == 'sampling_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Sampling Check Report (抜取検査報告)')->view('mails.sampling_check');
        }
        if($this->remark == 'laporan_aktivitas'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Laporan Aktivitas Audit (監査報告)')->view('mails.laporan_aktivitas');
        }
        if($this->remark == 'training'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Training Report (教育報告)')->view('mails.training');
        }
        if($this->remark == 'interview'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Interview Yubisashikosou Report (指差し呼称面談報告)')->view('mails.interview');
        }
        if($this->remark == 'daily_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Daily Check FG (日次完成品検査)')->view('mails.daily_check');
        }
        if($this->remark == 'labeling'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Labeling Safety Sign (安全ラベル表示)')->view('mails.labeling');
        }
        if($this->remark == 'audit_process'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Process (監査手順)')->view('mails.audit_process');
        }
        if($this->remark == 'first_product_audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Cek Produk Pertama Monthly Evidence (初物検査の監査　月次証拠)')->view('mails.first_product_audit');
        }
        if($this->remark == 'first_product_audit_daily'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Cek Produk Pertama Daily Evidence (初物検査の監査　日次証拠)')->view('mails.first_product_audit_daily');
        }
        if($this->remark == 'area_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Cek Kondisi Safety Area Kerja (職場安全状態確認)')->view('mails.area_check');
        }
        if($this->remark == 'kaizen'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Unverified Kaizen Teian')->view('mails.kaizen');
        }
        if($this->remark == 'jishu_hozen'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Implementasi Jishu Hozen (自主保全適用監査)')->view('mails.jishu_hozen');
        }
        if($this->remark == 'apd_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Cek Alat Pelindung Diri (APD) (保護具確認)')->view('mails.apd_check');
        }
        if($this->remark == 'weekly_report'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Weekly Activity Report (週次活動報告)')->view('mails.weekly_report');
        }
        if($this->remark == 'push_pull_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Push Pull Check Recorder (リコーダーのプッシュプル検査の不良報告)')->view('mails.push_pull_check');
        }
        if($this->remark == 'height_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Height Gauge Check Recorder (リコーダーの高さ検査の不良報告)')->view('mails.height_check');
        }
        if($this->remark == 'push_pull'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Push Pull & Camera Stamp Check Recorder (リコーダープッシュプールチェック)')->view('mails.push_pull');
        }
        if($this->remark == 'urgent_wjo'){
            if($this->data[0]->attachment != null){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Urgent Workshop Job Order (優先のワークショップ作業依頼書)')
                ->view('mails.urgent_wjo')
                ->attach(public_path('workshop/'.$this->data[0]->attachment));
            }else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Urgent Workshop Job Order (優先のワークショップ作業依頼書)')
                ->view('mails.urgent_wjo');
            }

        }
        if($this->remark == 'visitor_confirmation'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Visitor Confirmation (来客の確認)')->view('mails.visitor_confirmation');
        }
        if($this->remark == 'incoming_visitor'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Incoming Visitor (??)')->view('mails.incoming_visitor');
        }
        if($this->remark == 'ng_finding'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Temuan NG (??)')->view('mails.ng_finding');
        }
        if($this->remark == 'cpar_dept'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Ketidaksesuaian '.$this->data[0]->judul.' ')->view('mails.cpar_dept');
        }
        if($this->remark == 'rejectcpar_dept'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Ketidaksesuaian '.$this->data[0]->judul.' Telah Ditolak')->view('mails.rejectcpar_dept');
        }
        
        if($this->remark == 'std_audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit ISO Standarisasi')->view('mails.std_audit');
        }

        if($this->remark == 'reject_std_audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit ISO Standarisasi')->view('mails.std_audit');
        }

        if($this->remark == 'machine'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Machine Error Information (設備エラー情報)')->view('mails.machine_notification');
        }

        if($this->remark == 'urgent_spk'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Urgent Maintenance Job Order ()')->view('mails.urgent_spk');
        }

        if($this->remark == 'hrq'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Unanswered HR Question & Answer (HR Q&A)')->view('mails.hrq');
        }

        if($this->remark == 'purchase_requisition'){
            if($this->data[0]->file_pdf != null && $this->data[0]->file != null){
                $all_file = json_decode($this->data[0]->file);

                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Requisition '.$this->data[0]->no_pr.'')
                ->view('mails.purchase_requisition')
                ->attach(public_path('files/pr/'.$all_file[0]))
                ->attach(public_path('pr_list/'.$this->data[0]->file_pdf));
            }
            else if($this->data[0]->file_pdf != null){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Requisition '.$this->data[0]->no_pr.'')
                ->view('mails.purchase_requisition')
                ->attach(public_path('pr_list/'.$this->data[0]->file_pdf));
            }else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Requisition '.$this->data[0]->no_pr.'')
                ->view('mails.purchase_requisition');
            }
        }

        if($this->remark == 'purchase_order'){
            if($this->data[0]->file_pdf != null ){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Order '.$this->data[0]->no_po.'')
                ->view('mails.purchase_order')
                ->attach(public_path('po_list/'.$this->data[0]->file_pdf));
            } else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Order '.$this->data[0]->no_po.'')
                ->view('mails.purchase_order');
            }
        }

        if($this->remark == 'chemical_spk'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify Maintenance Job Order')->view('mails.verify_spk');
        }

        if($this->remark == 'apar'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify APAR Purchase Requisition')->view('mails.verify_spk');
        }
    }
}
