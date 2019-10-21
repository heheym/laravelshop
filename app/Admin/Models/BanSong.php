<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class BanSong extends Model
{
    protected $table = 'ban_songs';
    protected $primaryKey  = 'musicdbpk';
    public $timestamps = false;
}
