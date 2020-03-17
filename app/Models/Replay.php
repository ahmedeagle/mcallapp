<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Replay extends Model
{
	protected $table    ="ticket_replies";  
    public $timestamps  = true;
    protected $fillable = ['message','seen','FromUser','ticket_id'];
    protected $hidden   = ['updated_at'];
    
 
    function ticket()
    {
        return $this->belongsTo('App\Models\Ticket');
    }
}
