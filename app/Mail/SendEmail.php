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
        if($this->remark == 'clinic_visit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Clinic Visit Data')->view('mails.clinic_visit');
        }
        if($this->remark == 'audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit NG Jelas (生産監査報告)')->view('mails.audit');
        }
        if($this->remark == 'sampling_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Sampling Check Report (抜取検査報告)')->view('mails.sampling_check');
        }
        if($this->remark == 'laporan_aktivitas'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Laporan Aktivitas Audit IK (監査報告)')->view('mails.laporan_aktivitas');
        }
        if($this->remark == 'training'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Training Report (教育報告)')->view('mails.training');
        }
        if($this->remark == 'interview'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Interview Yubisashikosou Report (指差し呼称面談報告)')->view('mails.interview');
        }
        if($this->remark == 'daily_check'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Daily Check FG / KD (日次完成品検査)')->view('mails.daily_check');
        }
        if($this->remark == 'labeling'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Label Safety Mesin (安全ラベル表示)')->view('mails.labeling');
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
        if($this->remark == 'driver_request'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Driver Request Approval (??)')->view('mails.driver_request');
        }
        if($this->remark == 'driver_approval_notification'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Driver Request Approved (??)')->view('mails.driver_approval_notification');
        }
        if($this->remark == 'visitor_confirmation'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Visitor Confirmation (来客の確認)')->view('mails.visitor_confirmation');
        }
        if($this->remark == 'incoming_visitor'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Incoming Visitor (ご来社のお客様)')->view('mails.incoming_visitor');
        }
        if($this->remark == 'ng_finding'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->subject('Laporan Temuan NG')->view('mails.ng_finding');
        }
        if($this->remark == 'cpar_dept'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Laporan Ketidaksesuaian ')->view('mails.cpar_dept');
        }
        if($this->remark == 'rejectcpar_dept'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Laporan Ketidaksesuaian Tidak Disetujui')->view('mails.rejectcpar_dept');
        }
        
        if($this->remark == 'std_audit'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit ISO Standarisasi')->view('mails.std_audit');
        }

        if($this->remark == 'audit_all'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit MIRAI')->view('mails.audit_all');
        }

        if($this->remark == 'patrol'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit & Patrol MIRAI')->view('mails.patrol');
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
                ->subject('Purchase Requisition (購入申請)')
                ->view('mails.purchase_requisition')
                ->attach(public_path('files/pr/'.$all_file[0]))
                ->attach(public_path('pr_list/'.$this->data[0]->file_pdf));
            }
            else if($this->data[0]->file_pdf != null){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Requisition (購入申請)')
                ->view('mails.purchase_requisition')
                ->attach(public_path('pr_list/'.$this->data[0]->file_pdf));
            }else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Requisition (購入申請)')
                ->view('mails.purchase_requisition');
            }
        }

        if($this->remark == 'purchase_order'){
            if($this->data[0]->file_pdf != null ){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Order (発注依頼)')
                ->view('mails.purchase_order')
                ->attach(public_path('po_list/'.$this->data[0]->file_pdf));
            } else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Purchase Order (発注依頼)')
                ->view('mails.purchase_order');
            }
        }

        if($this->remark == 'new_agreement'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->priority(1)
            ->subject('New Agreement (????????)')
            ->view('mails.new_agreement')
            ->attach(public_path('files/agreements/'.$this->data[0]->file_name));
        }

        if($this->remark == 'update_agreement'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->priority(1)
            ->subject('Update Agreement (????????)')
            ->view('mails.new_agreement')
            ->attach(public_path('files/agreements/'.$this->data[0]->file_name));
        }

        if($this->remark == 'notif_agreement'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->priority(1)
            ->subject('Expiration Notification Agreement (????????)')
            ->view('mails.notif_agreement');
        }

        if($this->remark == 'investment'){
            if($this->data[0]->pdf != null && $this->data[0]->file != null){
                $all_file = json_decode($this->data[0]->file);

                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Investment - Expense Application (投資・経費申請)')
                ->view('mails.investment')
                ->attach(public_path('files/investment/'.$all_file[0]))
                ->attach(public_path('investment_list/'.$this->data[0]->pdf));
            }
            if($this->data[0]->pdf != null ){
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Investment - Expense Application (投資・経費申請)')
                ->view('mails.investment')
                ->attach(public_path('investment_list/'.$this->data[0]->pdf));
            } else{
                return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Investment - Expense Application (投資申請)')
                ->view('mails.investment');
            }
        }

        if($this->remark == 'sakurentsu'){
            if ($this->data[0]->position == 'interpreter') {
                if($this->data[0]->file != null){
                    $all_file = json_decode($this->data[0]->file);

                    $email = $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Sakurentsu (作連通)')
                    ->view('mails.sakurentsu');

                    for ($i=0; $i < count($all_file); $i++) { 
                        $email->attach(public_path('uploads/sakurentsu/original/'.$all_file[$i]));
                    }

                    return $email;
                }
            }
            else if ($this->data[0]->position == 'PC'){
                if($this->data[0]->file_translate != null){
                    $all_file = json_decode($this->data[0]->file_translate);

                    $email = $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Sakurentsu (作連通)')
                    ->view('mails.sakurentsu');

                    for ($i=0; $i < count($all_file); $i++) { 
                        $email->attach(public_path('uploads/sakurentsu/translated/'.$all_file[$i]));
                    }

                    return $email;
                }
            }
            else if ($this->data[0]->position == 'PIC'){
                $all_file = json_decode($this->data[0]->file_translate);

                $email = $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Sakurentsu (作連通)')
                ->view('mails.sakurentsu');

                for ($i=0; $i < count($all_file); $i++) { 
                    $email->attach(public_path('uploads/sakurentsu/translated/'.$all_file[$i]));
                }

                return $email;
            }
        }

        if($this->remark == '3m_approval'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('3M Application (3M申請書)')->view('mails.three_M_approval');
        }

        if($this->remark == '3m_document'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('3M Document(s) Requirement')->view('mails.three_M_document');
        }

        if($this->remark == 'transfer_budget'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->priority(1)
            ->subject('Transfer Budget')
            ->view('mails.transfer_budget');
        }

        if($this->remark == 'chemical_spk'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify Maintenance Job Order')->view('mails.verify_spk');
        }

        if($this->remark == 'apar'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify APAR Purchase Requisition')->view('mails.verify_spk');
        }

        if($this->remark == 'chemical_not_input'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->priority(1)
            ->subject('Input Production Result (Controlling Chart) Reminder')
            ->view('mails.chemical_not_input');
        }

        if($this->remark == 'safety_shoes'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->subject('Safety Shoes')
            ->view('mails.safety_shoes');
        }

        if($this->remark == 'spk_urgent'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->subject('Maintenance SPK Urgent Notification')
            ->view('mails.maintenance_urgent');
        }   

        if($this->remark == 'temperature'){
            return $this->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia')
            ->subject('Abnormal Employee Temperature')
            ->view('mails.temperature');
        }    
    }
}
