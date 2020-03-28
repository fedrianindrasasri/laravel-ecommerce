<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //relasi ke City.php
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
