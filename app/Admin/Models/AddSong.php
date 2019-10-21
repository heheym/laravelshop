<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AddSong extends Model
{
    protected $table = 'add_songs';
    protected $primaryKey  = 'bugeId';
    public $timestamps = false;

}
