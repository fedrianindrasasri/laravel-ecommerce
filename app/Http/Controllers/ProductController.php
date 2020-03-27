<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use Illuminate\Support\Str;
use File;

class ProductController extends Controller
{
    public function index()
    {
        /*
            query model product, dengan mengurutkan berdasarkan created-at
            buat relasi dengan dengan eager loading with()
            kategory adalah fungsi model yang berada pada Product Model
        */
        $product = Product::with(['category'])->orderBy('created_at', 'DESC');

        /*
            jika terdapat pencarian di url atau q pada url tidak kosong
        */
        if(request()->q != '')
        {
            /*
                maka lakukan filtering data berdasarkan name dan valuenya sesuai dengan pencarian yang dilakukan user
            */
            $product = $product->where('name', 'LIKE', '%' . request()->q . '%');
        }

        $product = $product->paginate(10);

        return view('products.index', compact('product'));
    }


    public function create()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('products.create', compact('category'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
        'name' => 'required|string|max:100',
        'description' => 'required',
        'category_id' => 'required|exists:categories,id', //CATEGORY_ID KITA CEK HARUS ADA DI TABLE CATEGORIES DENGAN FIELD ID
        'price' => 'required|integer',
        'weight' => 'required|integer',
        'image' => 'required|image|mimes:png,jpeg,jpg' //GAMBAR DIVALIDASI HARUS BERTIPE PNG,JPG DAN JPEG
        ]);

        // jika ada file image
        if($request->hasFile('image'))
        {
            // masukkan kedalam variabel file
            $file = $request->file('image');

            // kemudian nama filenya kita buat customer dengan perpaduan time dan slug dari nama product, extensinya bawaan dari file
            $filename = time().Str::slug($request->name).'.'.$file->getClientOriginalExtension();

            // simpan filenya di public/products, dan paramter kedua adalah nama filenya
            $file->storeAs('public/products', $filename);

            // setelah file disimpan kita simpan informasinya kedatabase
            $product = Product::create([
                'name' =>$request->name,
                'slug' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'image' => $filename,
                'price' => $request->price,
                'weight' => $request->weight,
                'status' => $request->status
            ]);

            return redirect(route('product.index'))->with(['succes' => 'Produk Baru Ditambahkan']);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        // delete file image
        File::delete(storage_path('app/public/products/'.$product->image));

        $product->delete();

        return redirect(route('product.index'))->with(['success' => 'Produk Sudah Dihapus']);
    }


}
