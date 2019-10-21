<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class DangerSong extends Model
{
    protected $table = 'danger_songs';
    protected $primaryKey  = 'musicdbpk';
    public $timestamps = false;
}
