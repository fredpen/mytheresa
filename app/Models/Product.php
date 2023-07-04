<?php

namespace App\Models;

use App\DataObjects\Products;

class Product
{
    /**
     * Get all products from the store(Json file)
     *
     * @return Products
     */
    public static function getProducts(): Products
    {
        $fakeDataPath = resource_path('Fakes/products.json');
        $productsData = file_get_contents($fakeDataPath);
        $products = json_decode($productsData);

        return (new Products($products->products))->init();
    }
}
 