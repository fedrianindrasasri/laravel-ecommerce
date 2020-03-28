@extends('layouts.ecommerce')

@section('title')
<title>Checkout - Ecommerce</title>
@endsection

@section('content')

<!--================Home Banner Area =================-->
<section class="banner_area">
    <div class="banner_inner d-flex align-items-center">
        <div class="overlay"></div>
        <div class="container">
            <div class="banner_content text-center">
                <h2>Informasi Pengiriman</h2>
                <div class="page_link">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="#">Checkout</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Home Banner Area =================-->


<!--================Checkout Area =================-->
<section class="checkout_area section_gap">
    <div class="container">
        <div class="billing_details">
            <div class="row">
                <div class="col-lg-8">
                    <h3>
                        Informasi Pengiriman
                    </h3>
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form class="row contact_form" action="{{ route('front.store_checkout') }}" method="post"
                        novalidate="novalidate">
                        @csrf
                        <div class="col-md-12 form-group p_star">
                            <label for="">Nama Lengkap</label>
                            <input type="text" name="customer_name" id="first" class="form-control" required>
                            <p class="text-danger">
                                {{ $errors->first('customer_name') }}
                            </p>
                        </div>

                        <div class="col-md-12 form-group p_star">
                            <label for="">No Telp</label>
                            <input type="text" name="customer_phone" id="number" class="form-control" required>
                            <p class="text-danger">
                                {{ $errors->first('customer_phone') }}
                            </p>
                        </div>

                        <div class="col-md-12 form-group p_star">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <p class="text-danger">
                                {{ $errors->first('email') }}
                            </p>
                        </div>

                        <div class="col-md-12 form-group p_star">
                            <label for="">Alamat Lengkap</label>
                            <input type="text" name="customer_address" id="add1" class="form-control" required>
                            <p class="text-danger">
                                {{ $errors->first('customer_address') }}
                            </p>
                        </div>

                        <div class="col-md-12 form-group p_star">
                            <label for="">Provinsi</label>
                            <select name="province_id" id="province_id" class="form-control" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-danger">
                                {{ $errors->first('province_id') }}
                            </p>
                        </div>

                        <!-- ADAPUN DATA KOTA DAN KECAMATAN AKAN DI RENDER SETELAH PROVINSI DIPILIH -->
                        <div class="col-md-12 form-group p_star">
                            <label for="">Kabupaten/Kota</label>
                            <select name="city_id" id="city_id" class="form-control" required>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            <p class="text-danger">
                                {{ $errors->first('city_id') }}
                            </p>
                        </div>
                        <div class="col-md-12 form-group p_star">
                            <label for="">Kecamatan</label>
                            <select name="district_id" id="district_id" class="form-control" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <p class="text-danger">
                                {{ $errors->first('district_id') }}
                            </p>
                        </div>
                        <!-- ADAPUN DATA KOTA DAN KECAMATAN AKAN DI RENDER SETELAH PROVINSI DIPILIH -->
                </div>
                <div class="col-lg-4">
                    <div class="order_box">
                        <h2>Ringkasan Pesanan</h2>
                        <ul class="list">
                            <li>
                                <a href="#">Product <span>Total</span></a>
                            </li>

                            @foreach ($carts as $cart)
                            <li>
                                <a href="#">
                                    {{ \Str::limit($cart['product_name'], 10) }}
                                    <span class="middle">x {{ $cart['qty'] }}</span>
                                    <span class="last">Rp {{ number_format($cart['product_price']) }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <ul class="list list_2">
                            <li>
                                <a href="#">Subtotal <span>Rp {{ number_format($subtotal) }}</span></a>
                            </li>
                            <li>
                                <a href="#">Pengiriman <span>Rp 0</span></a>
                            </li>
                            <li>
                                <a href="#">Total <span>Rp {{ number_format($subtotal) }}</span></a>
                            </li>
                        </ul>
                        <button class="main_btn">Bayar Pesanan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Checkout Area =================-->

@endsection


@section('js')
<script>
    //ketika select box dengan province id_dipilih
    $('#province_id').on('change', function() {
        // maka akan melakukan request ke url /api/city
        // dan mengirimkan data province_id
        $.ajax({
            url: "{{ url('/api/city') }}",
            type: "GET",
            data: { province_id: $(this).val() },
            success: function(html){
                // setea=lah data ditemia, selekbox city dikosongkan
                $('#city_id').empty()
                // kemudian append data baru dari hasil request via ajax
                // untuk menampilkan data kabupaten kota
                $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                $.each(html.data, function(key, item){
                    $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                    console.log(item.name);
                })
            }
        });
    })

    $('#city_id').on('change', function() {
        $.ajax({
            url: "{{ url('/api/district') }}",
            type: "GET",
            data: { city_id: $(this).val() },
            success: function(html){
                $('#district_id').empty()
                $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                $.each(html.data, function(key, item){
                    $('#district_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                    console.log(item.name);
                })
            }
        });
    })
</script>
@endsection
