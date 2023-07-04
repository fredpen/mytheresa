<?php

namespace App\DataObjects;

use Illuminate\Support\Collection;


class Products
{
    public $products;

    public function __construct(array $products)
    {
        $this->products = collect($products);
    }


    public function init(): self
    {
        return $this;
    }


    public function filterByCategory(string $queyParam): self
    {
        //convert all input to lower case to ensure
        // query is efficient with all cases
        $queryParam = strtolower($queyParam);
        $this->products = $this->products
            ->filter(fn ($product) => $product->category == $queryParam)
            ->values();

        return $this;
    }

    private function getDiscount(object $product): int
    {
        $discount = 0;

        if ($product->sku == "000003") {
            $discount = 15;
        }

        if ($product->category == "boots") {
            $discount = 30;
        }

        return $discount;
    }
 
    private function getFinalPrice(int $originalPrice, int $discount): int
    {
        return $discount == 0 ?
            $originalPrice :
            $originalPrice - ($originalPrice * $discount / 100);
    }

    public function applyDiscount(): self
    {
        $this->products = $this->products->map(function ($product) {

            $originalPrice = $product->price;
            $discount = $this->getDiscount($product);
            $finalPrice = $this->getFinalPrice($originalPrice, $discount);

            $product->price = [
                "original" => $originalPrice,
                "final" => $finalPrice,
                "discount_percentage" => $discount === 0 ?
                    $discount : "{$discount}%",
                "currency" => "EUR",
            ];

            return $product;
        })->values();

        return $this;
    }


    public function filterByPriceLessThan(int $price): self
    {
        $this->products = $this->products
            ->filter(fn ($product) => $product->price <= $price)
            ->values();

        return $this;
    }


    /**
     * Paginate products
     * Ensure max page count is 5
     *
     * @return Collection
     */
    public function paginate(int $page, int $perPage): Collection
    {
        return $this->products->forPage(
            $page,
            $perPage > 5 ? 5 : $perPage
        );
    }
}
