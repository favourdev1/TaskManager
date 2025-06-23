<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Auth::user()->tasks();

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority if provided
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title if provided
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderBy('due_date', 'asc')
                      ->orderBy('priority', 'desc')
                      ->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        Auth::user()->tasks()->create($validated);

        return redirect()->route('tasks.index')
                        ->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        // Set completed_at timestamp when marking as completed
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.index')
                        ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success', 'Task deleted successfully!');
    }

    /**
     * Mark a task as completed.
     */
    public function complete(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->markAsCompleted();

        return redirect()->back()
                        ->with('success', 'Task marked as completed!');
    }

    /**
     * Display the kanban board view with tasks grouped by status.
     */
    public function kanban(Request $request): View
    {
        $query = Auth::user()->tasks();

        // Filter by priority if provided
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title if provided
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Get all tasks matching the criteria
        $tasks = $query->get();

        // Group tasks by status
        $pendingTasks = $tasks->where('status', 'pending')->values();
        $inProgressTasks = $tasks->where('status', 'in_progress')->values();
        $completedTasks = $tasks->where('status', 'completed')->values();

        return view('tasks.kanban', compact('pendingTasks', 'inProgressTasks', 'completedTasks'));
    }

    /**
     * Update task status via AJAX for kanban board drag and drop.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        
        $status = $request->status;
        $oldStatus = $task->status;
        
        // Update task status
        $task->status = $status;
        
        // Set completed_at timestamp when marking as completed
        if ($status === 'completed' && $oldStatus !== 'completed') {
            $task->completed_at = now();
        } elseif ($status !== 'completed') {
            $task->completed_at = null;
        }
        
        $task->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
        ]);
    }
}
