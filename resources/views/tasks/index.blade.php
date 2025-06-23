<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Task
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden border sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap gap-4">
                        <div>
                            <input type="text" name="search" placeholder="Search tasks..."
                                   value="{{ request('search') }}"
                                   class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md border">
                        </div>
                        <div>
                            <select name="status" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md border">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div>
                            <select name="priority" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md border">
                                <option value="">All Priority</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                        <a href="{{ route('tasks.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="bg-white overflow-hidden border sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($tasks->count() > 0)
                        <div class="space-y-4">
                            @foreach($tasks as $task)
                                <div class="border rounded-lg p-4 {{ $task->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="text-lg font-semibold {{ $task->status === 'completed' ? 'line-through text-gray-500' : '' }}">
                                                    {{ $task->title }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($task->priority === 'high') bg-red-100 text-red-800
                                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($task->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </div>

                                            @if($task->description)
                                                <p class="text-gray-600 mb-2">{{ Str::limit($task->description, 100) }}</p>
                                            @endif

                                            @if($task->due_date)
                                                <p class="text-sm text-gray-500">
                                                    Due: {{ $task->due_date->format('M d, Y') }}
                                                    @if($task->isOverdue())
                                                        <span class="text-red-600 font-semibold">(Overdue)</span>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex gap-2 ml-4">
                                            @if($task->status !== 'completed')
                                                <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Complete
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('tasks.show', $task) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                View
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $tasks->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">No tasks found.</p>
                            <a href="{{ route('tasks.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Your First Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
