<?php

namespace Tests\Feature;

use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpenAIServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure the testing database exists and has the vector extension
        require_once __DIR__ . '/../setup-test-database.php';
    }

    #[Test]
    public function it_returns_mock_embedding_in_testing_environment()
    {
        $service = new OpenAIService();
        $embedding = $service->getEmbedding('Test message');

        $this->assertIsArray($embedding);
        $this->assertCount(1536, $embedding);
        $this->assertEquals(0.1, $embedding[0]);
    }

    #[Test]
    public function it_uses_correct_model_for_embeddings()
    {
        $service = new OpenAIService();
        $this->assertEquals('text-embedding-3-small', $service->getModel());
    }

    private function getModel()
    {
        return $this->model;
    }
} 