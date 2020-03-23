<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id', 'slug'];
    /*
        method untuk menghandle relationships
    */
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    /*
        untuk scope methodnya diawali dengan kata scope baru nama methodnya
    */
    public function scopeGetParent($query)
    {
        /*
            semua query yang menggunakan local coper ini akan menambahkan secara otomatis
            kondisi whereNull('parent_id')
        */
        return $query->whereNull('parent_id');
    }

    // mutator => bekerja memodifikasi dara sebelum data tersebut disimpan kedalam database
    public function setSlugeAttribute($value)
    {
        $this->attribute['slug'] = Str::slug($value);
    }

    // accessor => kebalikan dari mutator
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function child()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
