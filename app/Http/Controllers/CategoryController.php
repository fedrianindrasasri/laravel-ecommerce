<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// load model category
use App\Category;

class CategoryController extends Controller
{
    public function index()
    {
        /*
            buat query kedatabase dengan menggunakan model category dan diurutkan berdasarkan created-at, dan descending
            dan paginate(10), berarti hanya 10 data per pagenya.
            fungsi with(), disebut eager loading
            nama yang disebut didalamnya method yang didefinisikan didalam model category,
            berfungsi untuk relationship antar table
            jika lebih dari 1 maka dipisahkan dengan koma
            cth: with(['parent, 'contoh1','contoh2'])
        */
        $category = Category::with(['parent'])->orderBy('created_at', 'DESC')->paginate(10);

        /*
            query ini akan mengambil semua list category dari table categories
            lalu get() tanpa ada limit,
            getParent() adalah sebuah method local scope
        */
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();

        /*
            load view dari folder categories,
            kemudian passing data dari variable category dan parent agar bisa digunakan di view terkait
        */
        return view('categories.index', compact('category', 'parent'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:50|unique:categories'
        ]);

        $request->request->add(['slug' => $request->name]);

        // memasukkan request kecuali _token
        Category::create($request->except('_token'));

        return redirect(route('category.index'))->with(['success' => 'Kategori Baru Ditambahkan']);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();

        return view('categories.edit', compact('category', 'parent'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:categories,name,'.$id
        ]);

        $category = Category::find($id);

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect(route('category.index'))->with(['success' => 'Kategori Diperbaharui!']);
    }

    public function destroy($id)
    {
        $category = Category::withCount(['child'])->find($id);
        if($category->child_count == 0)
        {
            $category->delete();
            return redirect(route('category.index'))->with(['success' => 'Kategori Dihapus!!']);
        }

        return redirect(route('category.index'))->with(['error' => 'Kategori ini Memiliki Anak Kategori!!']);
    }
}
