<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTimeline extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id', 'pic_id', 'pic_name', 'timeline_category', 'timeline_description', 'duration', 'progress_update', 'remark', 'created_by', 'timeline_date', 'timeline_attachment'
    ];
}
