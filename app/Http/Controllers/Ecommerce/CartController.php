<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Province;
use App\City;
use App\District;
use App\Customer;
use App\Order;
use App\OrderDetail;
use Illuminate\Support\Str;
use DB;

class CartController extends Controller
{

    private function getCarts()
    {
        $carts = json_decode(request()->cookie('ecom-carts'), true);
        $carts = $carts != '' ? $carts:[];
        return $carts;
    }


    public function addToCart(Request $request)
    {
        // Validasi data yang dikirm
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer'
        ]);

        // ambil data dari cart cookie, karena bentuknya json maka kita gunakan json_decode untuk menjadikannya array
        $carts = $this->getCarts();

        // cek jika carts tidak nll dan product id ada didalam array carts
        if ($carts && array_key_exists($request->product_id, $carts)) {
        // maka update qty-nya berdasakan product_id yang dijadikan ke array
            $carts[$request->product_id]['qty'] += $request->qty;
        } else {
            // buat query untuk mengambil product_id sebagai key dari arrya carts
            $product = Product::find($request->product_id);
            // tambah data baru dengan menjadikan Product_id sebagai key dari array carts
            $carts[$request->product_id] = [
                'qty' => $request->qty,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image
            ];
        }

        // buat cookie dengan nama ecom-carts
        // jangan lupa untuk diencode kembali, dan limitnya 2800 menut atau 48 jam
        $cookie = cookie('ecom-carts', json_encode($carts), 2800);
        // store ke browser untuk disimpan
        return redirect()->back()->cookie($cookie);
    }

    public function listCart()
    {
        // ambil data dari cookie
        $carts = $this->getCarts();

        // ubah array menjadi collection, kemudian gunakan method sum untuk menghtiung totla
        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price']; //subtotal dari qty * price
        });

        // load view cart.blade.php dan passing data carts dan subtotal
        return view('ecommerce.cart', compact('carts', 'subtotal'));
    }

    public function updateCart(Request $request)
    {
        // ambil data dari cookie
        $carts = $this->getCarts();
        // looping data product_id, karena name array pada view sebelumnya
        // maka data yang diteima dalah array sehingga bisa di looping
        foreach($request->product_id as $key => $row)
        {
            // di check, jika qty dengan key yang sama dengan product_id = 0
            if($request->qty[$key] == 0)
            {
                // maka data tersebut dihapus dari array
                unset($carts[$row]);
            } else {
                // selain itu maka akan diperbaharui
                $carts[$row]['qty'] = $request->qty[$key];
            }
        }
        // set kembali cookirnya
        $cookie = cookie('ecom-carts', json_encode($carts), 2800);
        return redirect()->back()->cookie($cookie);
    }


    public function checkout()
    {
        // query mengambil semua data province
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        $carts = $this->getCarts(); //mengambil data carts
        // mrnghitung subtotal dari kelanjang belanja (cart)
        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price'];
        });

        // load view dan passing data
        return view('ecommerce.checkout', compact('provinces', 'carts', 'subtotal'));
    }

    public function getCity()
    {
        $cities = City::where('province_id', request()->province_id)->get();
        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)->get();
        return response()->json(['status' => 'success', 'data' => $districts]);
    }

    // fungsi untuk prosess checkout
    public function processCheckout(Request $request)
    {
        // validasi data
        $this->validate($request, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        // inisiasi database transaction
        // database transaction berfungsi untuk memastikan semua proses sukses untuk kemudian dicommit agar data benar-benar disimpan,
        // jioka terjadi error maka kita rollback agar datanya selaras

        DB::beginTransaction();

        try
        {
            // check data customer berdasarkan email
            $customer = Customer::where('email', $request->email)->first();
            // jika dia tidak login dan data customernya ada
            if(!auth()->check() && $customer) {
                // maka redirect dan tampilkan instruksi login
                return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
            }

            // ambil data keranjang
            $carts = $this->getCarts();

            // hitung subtotal belanjaan
            $subtotal = collect($carts)->sum(function($q) {
                return $q['qty'] * $q['product_price'];
            });

            //simpan data customer baru
            $customer = Customer::create([
                'name' => $request->customer_name,
                'email' => $request->email,
                'phone_number' => $request->customer_phone,
                'address' => $request->customer_address,
                'district_id' => $request->district_id,
                'status' => false
            ]);

            // simpan data order
            $order = Order::create([
                'invoice' => Str::random(4).'-'.time(), //invoice kita buat string random dan waktu
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'district_id' => $request->district_id,
                'subtotal' => $subtotal
            ]);

            // looping data di carts
            foreach ($carts as $row) {
                // ambil data produk berdasarkan product_id
                $product = Product::find($row['product_id']);
                // simpan orrder detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'price' => $row['product_price'],
                    'qty' => $row['qty'],
                    'weight' => $product->weight
                ]);
            }

            // Tidak terjadi error, maka ci=ommit datanya untuk menginformasikan bahwa data sudah fix disimpan
            DB::commit();

            $carts = [];
            // kosongkan data keranjang di cookie
            $cookie = cookie('ecom-carts', json_encode($carts), 2800);

            // redirect ke halaman transaksi
            return redirect(route('front.finish_checkout', $order->invoice))->cookie($cookie);
        }catch(Exception $e){
            // jika terjadi error, rollback datanya
            DB::rollback();
            //DAN KEMBALI KE FORM TRANSAKSI SERTA MENAMPILKAN ERROR
        return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function checkoutFinish($invoice)
    {
        // ambil data pesanan berdasarkan invoice
        $order = Order::with(['district.city'])->where('invoice', $invoice)->first();

        // load view checkout_finish, parsing data order
        return view('ecommerce.checkout_finish', compact('order'));
    }
}
