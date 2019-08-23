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
            return $this->from('ympimis@gmail.com')->subject('MIS Shipment Reminder')->view('mails.shipment');
        }
        if($this->remark == 'overtime'){
            return $this->from('ympimis@gmail.com')->subject('MIS Overtime Information')->view('mails.overtime');
        }
        if($this->remark == 'stuffing'){
            return $this->from('ympimis@gmail.com')->subject('MIS Stuffing Information')->view('mails.stuffing');
        }
        if($this->remark == 'min_queue'){
            return $this->from('ympimis@gmail.com')->subject('MIS Kanban Queue Information')->view('mails.min_queue');
        }

        if($this->remark == 'duobleserialnumber'){
            return $this->from('ympimis@gmail.com')->subject('MIS Double Serial Number Information')->view('mails.duobleserialnumber');
        }
    }
}
