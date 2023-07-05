<?php

namespace Tests\Unit;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_can_fetch_list_of_products(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function test_price_currency_is_always_euro(): void
    {
        $response = $this->get('/products');

        foreach ($response['data'] as $product) {
            $this->assertSame("EUR", $product['price']['currency']);
        }
    }

    public function test_final_and_original_prices_have_same_amount_if_no_discount(): void
    {
        $response = $this->get('/products');

        foreach ($response['data'] as $product) {
            if ($product['price']['discount_percentage'] == null) {
                $this->assertSame(
                    $product['price']['final'],
                    $product['price']['original'],
                );
            }
        }
    }

    public function test_products_can_be_filtred_with_query_parameter_category(): void
    {
        $response = $this->get('/products?category=boots');

        foreach ($response['data'] as $product) {
            $this->assertSame($product['category'], "boots");
        }
    }

    public function test_products_can_be_filtred_with_query_parameter_price(): void
    {
        $cutOffPrice = 70000;
        $response = $this->get("/products?priceLessThan={$cutOffPrice}");

        foreach ($response['data'] as $product) {
            $this->assertLessThanOrEqual($cutOffPrice, $product['price']['original']);
        }
    }


    public function test_products_in_the_boot_category_have_30_percent_discount(): void
    {
        $response = $this->get('/products?category=Boots');

        foreach ($response['data'] as $product) {
            $priceDiffInPercentage = $this->findDiscount(
                $product['price']['original'],
                $product['price']['final']
            );

            $this->assertSame(
                "{$priceDiffInPercentage}%",
                $product['price']['discount_percentage']
            );
        }
    }

    public function test_products_return_at_most_five_elements(): void
    {
        $response = $this->get('/products');
        $this->assertLessThanOrEqual(count($response['data']), 5);
    }


    public function test_products_has_all_required_fields(): void
    {
        $response = $this->get('/products');

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->hasAll(['message', 'data', "page", "perPage"])
        );

        foreach ($response['data'] as $product) {
            $this->assertArrayHasKey("sku", $product);
            $this->assertArrayHasKey("price", $product);
            $this->assertArrayHasKey("name", $product);
            $this->assertArrayHasKey("category", $product);
        }
    }

    private function findDiscount($originalPrice, $finalPrice)
    {
        $priceDiff = ($originalPrice - $finalPrice) / $originalPrice;
        return $priceDiff * 100;
    }
}
