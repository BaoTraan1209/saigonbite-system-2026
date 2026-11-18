<?php
namespace Tests\Feature;
use Tests\TestCase;
class HealthTest extends TestCase {
    public function test_home_page_is_available(): void {
        $this->get('/')->assertOk();
    }
}
