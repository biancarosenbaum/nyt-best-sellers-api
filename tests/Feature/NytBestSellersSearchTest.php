<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NytBestSellersSearchTest extends TestCase
{
    protected $apiHost;

    protected $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKey = 'avalidapikey';
        $this->apiHost = Config::get('nyt.host');

        Config::set('nyt.api_key', $this->apiKey);

        Http::fake([
            "{$this->apiHost}?api-key=aninvalidapikey" => Http::response(
                json_decode(file_get_contents('tests/stubs/nyt_api_search_invalid_api_key_status_401.json'), true),
                401
            ),
        ]);

        Http::fake([
            "{$this->apiHost}?api-key=avalidapikey" => Http::response(
                json_decode(file_get_contents('tests/stubs/nyt_api_search_status_200.json'), true),
                200
            ),
        ]);

        Http::fake([
            "{$this->apiHost}?api-key=avalidapikey&title=book" => Http::response(
                json_decode(file_get_contents('tests/stubs/nyt_api_search_title_book_status_200.json'), true),
                200
            ),
        ]);

        Http::fake([
            "{$this->apiHost}?api-key=avalidapikey&isbn=0399178570" => Http::response(
                json_decode(file_get_contents('tests/stubs/nyt_api_search_isbn_status_200.json'), true),
                200
            ),
        ]);

        Http::fake([
            "{$this->apiHost}?api-key=avalidapikey&offset=40" => Http::response(
                json_decode(file_get_contents('tests/stubs/nyt_api_search_offset_40_status_200.json'), true),
                200
            ),
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Config::set('nyt.api_key', null);
    }

    /** @test */
    public function the_search_route_exists()
    {
        $response = $this->get(route('api.best-sellers.search'));

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    /** @test */
    public function it_can_search()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), [
            'title' => 'book',
        ]);

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(['num_results' => 778]);
    }

    /** @test */
    public function api_host_config_variable_must_be_set()
    {
        Config::set('nyt.host', null);

        $response = $this->json('GET', route('api.best-sellers.search'));

        $response->assertStatus(401);

        Config::set('nyt.host', $this->apiHost);
    }

    /** @test */
    public function api_key_config_variable_must_be_set()
    {
        Config::set('nyt.api_key', null);

        $response = $this->json('GET', route('api.best-sellers.search'));

        $response->assertStatus(401);

        Config::set('nyt.api_key', $this->apiKey);
    }

    /** @test */
    public function api_key_must_be_valid()
    {
        Config::set('nyt.api_key', 'aninvalidapikey');

        $response = $this->json('GET', route('api.best-sellers.search'));
        $response->dump();

        $response->assertStatus(401);

        Config::set('nyt.api_key', $this->apiKey);
    }

    /** @test */
    public function it_can_search_by_a_valid_isbn()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['isbn' => '0399178570']);

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(['num_results' => 1]);
    }

    /** @test */
    public function isbn_must_be_10_or_13_digits()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['isbn' => '03991785700']);

        $response->assertJsonValidationErrors(['isbn']);
    }

    /** @test */
    public function isbn_must_not_end_with_a_semicolon()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['isbn' => '03991785700;']);

        $response->assertJsonValidationErrors(['isbn']);
    }

    /** @test */
    public function it_can_search_by_a_valid_offset()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['offset' => '40']);

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(['num_results' => 34253]);
    }

    /** @test */
    public function offset_must_be_numeric()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['offset' => 'otwell']);

        $response->assertJsonValidationErrors(['offset']);
    }

    /** @test */
    public function offset_must_be_a_numbers_divisible_by_20()
    {
        $response = $this->json('GET', route('api.best-sellers.search'), ['offset' => 34]);

        $response->assertJsonValidationErrors(['offset']);
    }
}
