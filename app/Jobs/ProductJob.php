<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// import class productimport untuk menghandle file excelnya
use App\Imports\ProductImport;
use Illuminate\Support\Str;
use App\Product;
use File;

class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;
    protected $filename;


    /**
        karena dispatch mengirim dua paremeter maka kita terima keduanya
     */
    public function __construct($category, $filename)
    {
        $this->category = $category;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
            kemudian kita gunakan productimport yang merupakan class yang akan dibuat selanjutnya
            import data excel tadi yang sudah disimpan di storage, kemudian convert kedalam array
        */
        $files = (new ProductImport)->toArray(storage_path('app/public/uploads/'.$this->filename));

        // looping datanya
        foreach($files[0] as $row)
        {
            /*
                FORMATTING URLNYA UNTUK MENGAMBIL FILE-NAMENYA BESERTA EXTENSION
                JADI PASTIKAN PADA TEMPLATE MASS UPLOADNYA NANTI PADA BAGIAN URL
                HARUS DIAKHIRI DENGAN NAMA FILE YANG LENGKAP DENGAN EXTENSION
            */
            $explodeURL = explode('/', $row[4]);
            $explodeExtension = explode('.', end($explodeURL));
            $filename = time().Str::random(6).'.'.end($explodeExtension);

            // download gambar dari url terkait
            file_put_contents(storage_path('app/public/products').'/'.$filename, file_get_contents($row[4]));

            Product::create([
                'name' => $row[0],
                'slug' => $row[0],
                'category_id' => $this->category,
                'description' => $row[1],
                'price' => $row[2],
                'weight' => $row[3],
                'image' => $filename,
                'status' => true
            ]);
        }

        File::delete(storage_path('app/public/uploads/'.$this->filename));
    }
}
