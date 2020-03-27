<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{

    /*
        jika fillabel mengizinkan apa saja yang ada didalam arraynya
        maka guraded sebaliknya
        jadi apabila dielnya banyak, kita bisa memanfaatkan guarded dengan array kosong saja
    */

    protected $guarded = [];

    /*
        ini adalah assesor, kita membuat kolom baru bernama status_label
        kolom tersebut dihasikan oleh assesoe, walaupun tidak ada di table product
        akan tetapi disertakan di hasil query
    */

    public function getStatusLabelAttribute()
    {
        if ($this->status = 0)
        {
            return '<span class="badge badge-secondary">Draft</span>';
        }
        return '<span class="badge badge-success">Aktif</span>';
    }

    /*
        fungsi untuk menahndel relasi ke table category
    */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ini adalah fungsi mutators
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
