<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if authenticated user can list Todos.
     */
    public function test_user_can_list_todos()
    {
        $user = User::factory()->create();

        Todo::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/todos');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test if user can create a new Todo.
     */
    public function test_user_can_create_todo()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'New Todo',
            'content' => 'This is a new todo.',
            'color' => 'orange',
            'favorite' => false,
        ];

        $user_login = $this->actingAs($user, 'sanctum');
        $response = $user_login->postJson('/api/todos', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment($data);
    }

    /**
     * Test if user can show specific Todo.
     */
    public function test_user_can_view_todo()
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $user_login = $this->actingAs($user, 'sanctum');
        $response = $user_login->getJson('/api/todos/' . $todo->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $todo->title]);
    }

    /**
     * Test if user can update Todo.
     */
    public function test_user_can_update_todo()
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => 'Updated Todo',
            'content' => 'Updated content for the todo.',
            'color' => 'blue',
            'favorite' => true,
        ];

        $user_login = $this->actingAs($user, 'sanctum');
        $response = $user_login->putJson('/api/todos/' . $todo->id, $data);

        $response->assertStatus(200);
        $response->assertJsonFragment($data);
    }

    /**
     * Test if user can delete Todo.
     */
    public function test_user_can_delete_todo()
    {
        $user = User::factory()->create();

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $user_login = $this->actingAs($user, 'sanctum');
        $response = $user_login->deleteJson('/api/todos/' . $todo->id);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Todo deleted',
            'data' => [],
        ]);

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    /**
     * Test if user try find Todo that not exist.
     */
    public function test_user_cannot_view_non_existent_todo()
    {
        $user = User::factory()->create();

        $user_login = $this->actingAs($user, 'sanctum');
        $response = $user_login->getJson('/api/todos/9999');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Todo not found',
            'data' => [],
        ]);
    }
}
