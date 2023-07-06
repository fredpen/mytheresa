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


    /**
     * Setter
     */
    public function init(): self
    {
        return $this;
    }

    /**
     * filter By Category
     *
     * @return self
     */
    public function filterByCategory(string $queyParam): self
    {
        //convert all input to lower case to ensure
        // query is efficient against all cases
        $queryParam = strtolower($queyParam);
        $this->products = $this->products
            ->filter(fn ($product) => $product->category == $queryParam)
            ->values();

        return $this;
    }



    /**
     * Apply discount on products
     *
     * @return self
     */
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


    /**
     * filter By Price
     *
     * @return self
     */
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
     * @param int $page
     * @param int $perPage
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

    /**
     * Calculate the right discount of a product
     */
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

    /**
     * Get final price based off base price and discount
     */
    private function getFinalPrice(int $originalPrice, int $discount): int
    {
        return $discount == 0 ?
            $originalPrice :
            $originalPrice - ($originalPrice * $discount / 100);
    }
}
