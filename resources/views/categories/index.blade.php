@extends('layouts.admin')

@section('title')
<title>List Category</title>
@endsection

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Kategori</li>
    </ol>

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <!-- pada bagian ini akan menghanlde form input category-->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Kategori Baru
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('category.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Kategori</label>
                                    <input type="text" name="name" class="form-control" required>
                                    <p class="text-danger">{{ $errors->first('name')}}</p>
                                </div>

                                <div class="form-group">
                                    <label for="parent_id">Kategori</label>
                                    <!-- VARIABLE $PARENT PADA METHOD INDEX KITA GUNAKAN DISINI -->
                                    <!-- UNTUK MENAMPILKAN DATA CATEGORY YANG PARENT_ID NYA NULL -->
                                    <!-- UNTUK DIPILIH SEBAGAI PARENT TAPI SIFATNYA OPTIONAL -->
                                    <select name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @foreach ($parent as $row)
                                        <option value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>

                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--Akhir handle category baru-->

                <!--Handle table list category-->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                List Kategori
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                            <div class="alert alert-success">{{session('success')}}</div>
                            @endif

                            @if (session('error'))
                            <div class="alert alert-danger">{{session('error')}}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kategori</th>
                                            <th>Parent</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Looping data kategori sesuai jumlah data yang ada di $category-->
                                        @forelse ($category as $val)
                                        <tr>
                                            <td></td>
                                            <td>
                                                <strong>
                                                    {{$val->name}}
                                                </strong>
                                            </td>
                                            <td>
                                                {{ $val->parent ? $val->parent->name:'-' }}
                                            </td>
                                            <td>
                                                {{ $val->created_at->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                <form action="{{route('category.destroy', $val->id )}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{route('category.edit', $val->id)}}"
                                                        class="btn btn-warning btn-sm">Edit
                                                    </a>
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak Ada Data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{-- pagination secara otomatis --}}
                            {!! $category->links() !!}
                        </div>
                    </div>
                </div>
                <!--akhir handle list category-->
            </div>
        </div>
    </div>
</main>
@endsection
