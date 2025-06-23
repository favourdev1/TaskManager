<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Details') }}
            </h2>
            <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <div class="flex justify-between items-start mb-4">
                            <h1 class="text-2xl font-bold {{ $task->status === 'completed' ? 'line-through text-gray-500' : '' }}">
                                {{ $task->title }}
                            </h1>
                            <div class="flex gap-2">
                                @if($task->status !== 'completed')
                                    <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Mark as Complete
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Task
                                </a>
                            </div>
                        </div>

                        <div class="flex gap-4 mb-4">
                            <span class="px-3 py-1 text-sm rounded-full
                                @if($task->priority === 'high') bg-red-100 text-red-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($task->priority) }} Priority
                            </span>
                            <span class="px-3 py-1 text-sm rounded-full
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>

                    @if($task->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Description</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="whitespace-pre-wrap">{{ $task->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Task Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium text-gray-600">Created:</span>
                                    <span class="ml-2">{{ $task->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Last Updated:</span>
                                    <span class="ml-2">{{ $task->updated_at->format('M d, Y g:i A') }}</span>
                                </div>
                                @if($task->due_date)
                                    <div>
                                        <span class="font-medium text-gray-600">Due Date:</span>
                                        <span class="ml-2 {{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $task->due_date->format('M d, Y') }}
                                            @if($task->isOverdue())
                                                (Overdue)
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                @if($task->completed_at)
                                    <div>
                                        <span class="font-medium text-gray-600">Completed:</span>
                                        <span class="ml-2 text-green-600">{{ $task->completed_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($task->isOverdue() || $task->due_date)
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Timeline</h3>
                                <div class="space-y-2">
                                    @if($task->due_date)
                                        @php
                                            $daysUntilDue = now()->diffInDays($task->due_date, false);
                                        @endphp
                                        <div class="p-3 rounded-lg {{ $task->isOverdue() ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                                            @if($task->isOverdue())
                                                <p class="text-red-800">
                                                    <strong>Overdue by {{ abs($daysUntilDue) }} day{{ abs($daysUntilDue) !== 1 ? 's' : '' }}</strong>
                                                </p>
                                            @elseif($daysUntilDue === 0)
                                                <p class="text-orange-800">
                                                    <strong>Due Today!</strong>
                                                </p>
                                            @else
                                                <p class="text-blue-800">
                                                    <strong>{{ $daysUntilDue }} day{{ $daysUntilDue !== 1 ? 's' : '' }} remaining</strong>
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-6">
                        <div class="flex justify-between items-center">
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Delete Task
                                </button>
                            </form>

                            <div class="text-sm text-gray-500">
                                Task ID: #{{ $task->id }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
