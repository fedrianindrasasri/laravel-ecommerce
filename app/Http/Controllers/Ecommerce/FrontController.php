<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('ecommerce.index', compact('products'));
    }

    public function product()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(12);
        // queri dari categories sudah dibuat di View Composer
        return view('ecommerce.product', compact('products'));
    }

    // method untuk kehalaman category yang diklik
    public function categoryProduct($slug)
    {
        /*
            querynya mencari terlebih dahulu kategory berdasrkan slug,
            setelah data ditemukan, maka slug akan mengambil data product yang berelasi
            mengunakan method product yang telah didefiniskan pada file Category.php
            dan diurutkan berdasarkan created_at dan diload 12 data per sekali load
        */
        $products = Category::where('slug', $slug)->first()->product()->orderBy('created_at', 'DESC')->paginate(12);
        // load view yang sama yaitu product.balde.php, karena tampilannya akan kita buat sama juga
        return view('ecommerce.product', compact('products'));
    }

    public function show($slug)
    {
        // query untuk mengambil single data berdasarkan slug-nya
        $product = Product::with(['category'])->where('slug', $slug)->first();

        // load view show.blade.php
        return view('ecommerce.show', compact('product'));
    }

}
