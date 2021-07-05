<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketEquipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id', 'item_id', 'item_description', 'item_price', 'quantity', 'remark', 'created_by'   
    ];    
}
