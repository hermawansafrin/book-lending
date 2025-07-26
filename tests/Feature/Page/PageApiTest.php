<?php

namespace Tests\Feature\Page;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    /**
     * Test if main page redirects to api documentation page
     */
    public function test_main_page_redirects_to_api_documentation(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302); // redirect to api documentation page

        $response->assertRedirect('/api/documentation'); // must on api/documentation swagger
    }

    /**
     * Test if api documentation on swagger ui page
     */
    public function test_api_documentation_on_swagger_ui_page(): void
    {
        $response = $this->get('/api/documentation');
        $response->assertStatus(200); // must be successfull

        $response->assertSee('Swagger'); // must see swagger ui page
        $response->assertSee('Book Lending API'); // must see book lending api's
    }
}
