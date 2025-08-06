<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all news
     */
    public function test_can_get_all_news()
    {
        // 1. ARRANGE - Create 3 fake news items
        News::factory()->count(3)->create();

        // 2. ACT - Call our API
        $response = $this->getJson('/api/news');

        // 3. ASSERT - Check it worked
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Check we got 3 items back
        $data = $response->json('data');
        $this->assertCount(3, $data);
    }

    /**
     * Test creating news
     */
    public function test_can_create_news()
    {
        // 1. ARRANGE - Prepare data to send
        $newsData = [
            'title' => 'Test News Title',
            'link' => 'https://example.com/test-news'
        ];

        // 2. ACT - Call our CREATE API
        $response = $this->postJson('/api/news', $newsData);

        // 3. ASSERT - Check it worked
        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'News created successfully'
        ]);

        // Check database has the record
        $this->assertDatabaseHas('news', [
            'title' => 'Test News Title',
            'link' => 'https://example.com/test-news'
        ]);
    }

    /**
     * Test updating news
     */
    public function test_can_update_news()
    {
        // 1. ARRANGE - Create a news item first
        $news = News::factory()->create([
            'title' => 'Original Title',
            'link' => 'https://original.com'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'link' => 'https://updated.com'
        ];

        // 2. ACT - Call our UPDATE API
        $response = $this->patchJson("/api/news/{$news->id}", $updateData);

        // 3. ASSERT - Check it worked
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'News item updated successfully'
        ]);

        // Check database was actually updated
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated Title',
            'link' => 'https://updated.com'
        ]);
    }

    /**
     * Test deleting news
     */
    public function test_can_delete_news()
    {
        // 1. ARRANGE - Create a news item first
        $news = News::factory()->create([
            'title' => 'News to Delete'
        ]);

        // 2. ACT - Call our DELETE API
        $response = $this->deleteJson("/api/news/{$news->id}");

        // 3. ASSERT - Check it worked
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'News item deleted successfully'
        ]);

        // Check database no longer has the record
        $this->assertDatabaseMissing('news', [
            'id' => $news->id
        ]);
    }

    /**
     * Test getting single news item
     */
    public function test_can_get_single_news()
    {
        // 1. ARRANGE - Create a news item
        $news = News::factory()->create([
            'title' => 'Specific News Title'
        ]);

        // 2. ACT - Call our SHOW API
        $response = $this->getJson("/api/news/{$news->id}");

        // 3. ASSERT - Check it worked
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'title' => 'Specific News Title'
            ]
        ]);
    }

    /**
     * Test creating news with invalid data
     */
    public function test_cannot_create_news_with_invalid_data()
    {
        // Test missing title
        $response = $this->postJson('/api/news', [
            'link' => 'https://example.com'
        ]);
        $response->assertStatus(422); // Validation error

        // Test invalid URL
        $response = $this->postJson('/api/news', [
            'title' => 'Test Title',
            'link' => 'not-a-valid-url'
        ]);
        $response->assertStatus(422); // Validation error

        // Test title too long
        $response = $this->postJson('/api/news', [
            'title' => str_repeat('a', 256), // 256 chars, max is 255
            'link' => 'https://example.com'
        ]);
        $response->assertStatus(422); // Validation error
    }

    /**
     * Test updating with invalid data
     */
    public function test_cannot_update_news_with_invalid_data()
    {
        $news = News::factory()->create();

        // Test invalid URL
        $response = $this->patchJson("/api/news/{$news->id}", [
            'link' => 'not-a-valid-url'
        ]);
        $response->assertStatus(422); // Validation error
    }

    /**
     * Test getting non-existent news
     */
    public function test_returns_404_for_non_existent_news()
    {
        $response = $this->getJson('/api/news/999');
        $response->assertStatus(404);
    }

    /**
     * Test updating non-existent news
     */
    public function test_cannot_update_non_existent_news()
    {
        $response = $this->patchJson('/api/news/999', [
            'title' => 'Updated Title'
        ]);
        $response->assertStatus(500); // Your current error handling returns 500
    }

    /**
     * Test deleting non-existent news
     */
    public function test_cannot_delete_non_existent_news()
    {
        $response = $this->deleteJson('/api/news/999');
        $response->assertStatus(500); // Your current error handling returns 500
    }

    /**
     * Test pagination works
     */
    public function test_pagination_works()
    {
        // Create 15 news items
        News::factory()->count(15)->create();

        // Request 5 per page
        $response = $this->getJson('/api/news?per_page=5');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(5, $data); // Should get exactly 5 items
        
        // Check pagination info
        $this->assertEquals(5, $response->json('pagination.per_page'));
        $this->assertEquals(15, $response->json('pagination.total'));
        $this->assertEquals(3, $response->json('pagination.last_page')); // 15/5 = 3 pages
    }

    /**
     * Test empty update request
     */
    public function test_handles_empty_update_request()
    {
        $news = News::factory()->create();

        $response = $this->patchJson("/api/news/{$news->id}", []);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'No valid data provided for update'
                ]);
    }
}