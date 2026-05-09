<?php

namespace Tests\Feature\Todo;

use Tests\TestCase;
use App\Models\Todo;
use App\Models\TodoType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListTodosTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_list_paginated_todos(): void
    {
        $type = TodoType::factory()->create();

        Todo::factory()->count(15)->create([
            'todo_type_id' => $type->id,
        ]);

        $response = $this->getJson("/api/todo-types/{$type->id}/todos");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'next_page',
                'total',
                'per_page',
            ]);

        $this->assertCount(10, $response->json('data'));
    }
}