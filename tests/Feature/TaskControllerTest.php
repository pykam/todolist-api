<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;

class TaskControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    /**
     * A basic feature test example.
     */
    public function test_the_api_returns_a_successful_response(): void
    {
        $response = $this->get('/api/tasks');

        $response->assertStatus(200);
    }

    /**
     * Checking pagination
     */
    public function test_can_get_paginated_list_of_tasks(): void
    {
        Task::factory()->count(15)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_can_create_a_new_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => true
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'status',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending'
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);
    }

    public function test_validates_required_fields_when_creating_task()
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_show_a_specific_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [ // Добавьте этот уровень
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description
                ]
            ]);
    }

    public function test_returns_404_when_task_not_found()
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    public function test_can_update_an_existing_task()
    {
        $task = Task::factory()->create([
            'title' => 'Old Title',
            'status' => false
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'status' => true
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Task updated successfully',
                'data' => [
                    'title' => 'Updated Title',
                    'status' => 1
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 1
        ]);
    }

    public function test_validates_data_when_updating_task()
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => '', // Empty title
            'status' => 'invalid_status' // Invalid status
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully'
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_returns_error_when_deleting_non_existent_task()
    {
        $response = $this->deleteJson('/api/tasks/999');

        $response->assertStatus(404);
    }

}
