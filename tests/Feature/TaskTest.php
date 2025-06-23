<?php

use App\Models\Task;
use App\Models\User;

test('authenticated user can view tasks index page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('tasks.index'));
    
    $response->assertOk();
    $response->assertViewIs('tasks.index');
});

test('authenticated user can create a task', function () {
    $user = User::factory()->create();
    
    $taskData = [
        'title' => 'Test Task',
        'description' => 'This is a test task description',
        'priority' => 'high',
        'due_date' => now()->addDays(7)->format('Y-m-d'),
    ];
    
    $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);
    
    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('success', 'Task created successfully!');
    
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'user_id' => $user->id,
        'priority' => 'high',
    ]);
});

test('user can view their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->get(route('tasks.show', $task));
    
    $response->assertOk();
    $response->assertViewIs('tasks.show');
    $response->assertViewHas('task', $task);
});

test('user cannot view another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);
    
    $response = $this->actingAs($user1)->get(route('tasks.show', $task));
    
    $response->assertForbidden();
});

test('user can update their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    $updateData = [
        'title' => 'Updated Task Title',
        'description' => 'Updated description',
        'status' => 'in_progress',
        'priority' => 'low',
        'due_date' => now()->addDays(5)->format('Y-m-d'),
    ];
    
    $response = $this->actingAs($user)->put(route('tasks.update', $task), $updateData);
    
    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('success', 'Task updated successfully!');
    
    $task->refresh();
    expect($task->title)->toBe('Updated Task Title');
    expect($task->status)->toBe('in_progress');
    expect($task->priority)->toBe('low');
});

test('user can mark task as completed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending'
    ]);
    
    $response = $this->actingAs($user)->patch(route('tasks.complete', $task));
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Task marked as completed!');
    
    $task->refresh();
    expect($task->status)->toBe('completed');
    expect($task->completed_at)->not->toBeNull();
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));
    
    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('success', 'Task deleted successfully!');
    
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('user cannot update another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);
    
    $response = $this->actingAs($user1)->put(route('tasks.update', $task), [
        'title' => 'Hacked Task',
        'status' => 'completed',
        'priority' => 'high',
    ]);
    
    $response->assertForbidden();
});

test('task validation works correctly', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => '', // Required field missing
        'priority' => 'invalid', // Invalid priority
        'due_date' => '2020-01-01', // Past date
    ]);
    
    $response->assertSessionHasErrors(['title', 'priority', 'due_date']);
});

test('tasks can be filtered by status', function () {
    $user = User::factory()->create();
    Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
    Task::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    
    $response = $this->actingAs($user)->get(route('tasks.index', ['status' => 'pending']));
    
    $response->assertOk();
    $response->assertSee('pending');
});

test('tasks can be searched by title', function () {
    $user = User::factory()->create();
    Task::factory()->create(['user_id' => $user->id, 'title' => 'Important Meeting']);
    Task::factory()->create(['user_id' => $user->id, 'title' => 'Buy Groceries']);
    
    $response = $this->actingAs($user)->get(route('tasks.index', ['search' => 'Meeting']));
    
    $response->assertOk();
    $response->assertSee('Important Meeting');
    $response->assertDontSee('Buy Groceries');
});

test('overdue task detection works correctly', function () {
    $user = User::factory()->create();
    $overdueTask = Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => now()->subDays(1),
        'status' => 'pending'
    ]);
    
    expect($overdueTask->isOverdue())->toBeTrue();
    
    $completedOverdueTask = Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => now()->subDays(1),
        'status' => 'completed'
    ]);
    
    expect($completedOverdueTask->isOverdue())->toBeFalse();
});

test('guest cannot access task routes', function () {
    $task = Task::factory()->create();
    
    $this->get(route('tasks.index'))->assertRedirect('/login');
    $this->get(route('tasks.create'))->assertRedirect('/login');
    $this->get(route('tasks.show', $task))->assertRedirect('/login');
    $this->post(route('tasks.store'))->assertRedirect('/login');
});
