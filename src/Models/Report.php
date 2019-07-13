<?php

namespace Ibarts\Reportsystem\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    protected $table = 'reports';

    protected $fillable = [
        'userid',
        'report_date',
        'status',
        'percent_complete',
        'percent_extra',
        'comments',
    ];
}
