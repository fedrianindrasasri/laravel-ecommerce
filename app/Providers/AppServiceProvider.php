<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
            membantu meneruskan data view composer kedalam View dengan bantuan providers ini
            Parameter pertama berisi tujuan dimana data dari View Composer akan di-passing,
            kita masukkan ecommerce.* yang berarti semua file yang berada didalam folder resources/views/ecommerce.
            Parameter kedua adalah sumber datanya, maka dalam hal ini adalah CategoryComposer
        */
        View::composer('ecommerce.*', 'App\Http\View\CategoryComposer');
    }
}
