<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pic_id', 'pic_name', 'pic_position', 'remark', 'created_by'
    ];
}
