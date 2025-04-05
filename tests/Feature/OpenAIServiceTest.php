<?php

namespace Tests\Feature;

use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAIServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_embeddings()
    {
        // Mock the HTTP response
        Http::fake([
            'api.openai.com/v1/embeddings' => Http::response([
                'data' => [
                    [
                        'embedding' => array_fill(0, 1536, 0.1)
                    ]
                ]
            ], 200)
        ]);

        $service = new OpenAIService();
        $embedding = $service->getEmbedding('Test message');

        $this->assertIsArray($embedding);
        $this->assertCount(1536, $embedding);
        $this->assertEquals(0.1, $embedding[0]);
    }

    /** @test */
    public function it_throws_exception_on_api_error()
    {
        // Mock a failed HTTP response
        Http::fake([
            'api.openai.com/v1/embeddings' => Http::response([
                'error' => 'API Error'
            ], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to get embedding');

        $service = new OpenAIService();
        $service->getEmbedding('Test message');
    }

    /** @test */
    public function it_uses_correct_model_for_embeddings()
    {
        Http::fake([
            'api.openai.com/v1/embeddings' => Http::response([
                'data' => [
                    [
                        'embedding' => array_fill(0, 1536, 0.1)
                    ]
                ]
            ], 200)
        ]);

        $service = new OpenAIService();
        $service->getEmbedding('Test message');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/embeddings' &&
                   $request['model'] === 'text-embedding-3-small';
        });
    }
} 