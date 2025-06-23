<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 text-sm">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Task Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                @php
                    $totalTasks = auth()->user()->tasks()->count();
                    $pendingTasks = auth()->user()->tasks()->where('status', 'pending')->count();
                    $inProgressTasks = auth()->user()->tasks()->where('status', 'in_progress')->count();
                    $completedTasks = auth()->user()->tasks()->where('status', 'completed')->count();
                    $overdueTasks = auth()->user()->tasks()->where('due_date', '<', now())->where('status', '!=', 'completed')->count();
                @endphp

                <div class="bg-white overflow-hidden border rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $totalTasks }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalTasks }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden border rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $pendingTasks }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pendingTasks }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden border rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $completedTasks }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $completedTasks }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden border rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">{{ $overdueTasks }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $overdueTasks }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden border-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Task
                        </a>
                        <a href="{{ route('tasks.kanban') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            View Kanban Board
                        </a>
                        {{-- <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            View List View
                        </a> --}}
                        @if($overdueTasks > 0)
                            <a href="{{ route('tasks.kanban', ['status' => 'pending']) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                View Overdue Tasks
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="bg-white overflow-hidden border-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Tasks</h3>
                        <a href="{{ route('tasks.index') }}" class="text-blue-500 hover:text-blue-700">View All</a>
                    </div>

                    @php
                        $recentTasks = auth()->user()->tasks()->latest()->take(5)->get();
                    @endphp

                    @if($recentTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentTasks as $task)
                                <div class="flex justify-between items-center p-3 border rounded {{ $task->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                                    <div class="flex-1">
                                        <h4 class="font-medium {{ $task->status === 'completed' ? 'line-through text-gray-500' : '' }}">
                                            {{ Str::limit($task->title, 50) }}
                                        </h4>
                                        <div class="flex gap-2 mt-1">
                                            <span class="px-2 py-1 text-xs rounded
                                                @if($task->status === 'completed') bg-green-100 text-green-800
                                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            @if($task->due_date)
                                                <span class="text-xs text-gray-500">
                                                    Due: {{ $task->due_date->format('M d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:text-blue-700 ml-4">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No tasks yet. Create your first task to get started!</p>
                            <a href="{{ route('tasks.create') }}" class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
