<?php

namespace App\Http\View;

use Illuminate\View\View;
use App\Category;

class CategoryComposer
{
    public function compose(View $view)
    {
        // query categories kita tarok disini agar bisa dipakai di class lainnya
        $categories = Category::with(['child'])->withCount(['child'])->getParent()->orderBy('name', 'ASC')->get();
        // kemudian kita parsing data nya dengan nama variable categories
        $view->with('categories', $categories);
    }
}

?>
