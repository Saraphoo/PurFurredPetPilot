<?php

namespace App\Models;

use App\Services\OpenAIService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'response',
        'embedding'
    ];

    protected $casts = [
        'embedding' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findSimilar(string $query, int $limit = 5): array
    {
        $openAIService = app(OpenAIService::class);
        $embedding = $openAIService->getEmbedding($query);
        
        return self::select('message', 'response')
            ->orderByRaw('embedding <=> ?', [$embedding])
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function createWithEmbedding(array $attributes): self
    {
        $openAIService = app(OpenAIService::class);
        $embedding = $openAIService->getEmbedding($attributes['message']);
        
        return self::create([
            'user_id' => $attributes['user_id'],
            'message' => $attributes['message'],
            'response' => $attributes['response'],
            'embedding' => $embedding,
        ]);
    }
} 