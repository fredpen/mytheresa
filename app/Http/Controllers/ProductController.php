<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    /**
     * Get list of products
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProducts(Request $request): JsonResponse
    {
        $products =  Product::getProducts();

        //filter by category
        if ($request->has('category')) {
            $products->filterByCategory($request->category);
        }

        // filter by price
        if ($request->has('priceLessThan') && is_numeric($request->priceLessThan)) {
            $products->filterByPriceLessThan($request->priceLessThan);
        }

        // apply discount
        $products->applyDiscount();

        // paginate the products
        $products->paginate(
            $page = $request->page ?? 1,
            $perPage = $request->perPage ?? 5
        );

        // return a json response
        return response()->json([
            "message" => "success",
            "data" => $products->products,
            "page" => $page,
            "perPage" => $perPage,
        ], 200);
    }
}
