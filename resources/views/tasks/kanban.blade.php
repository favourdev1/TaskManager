<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Board') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    List View
                </a>
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Task
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 text-sm" x-data="kanbanBoard()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden border rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('tasks.kanban') }}" class="flex flex-wrap gap-4 text-sm">
                        <div>
                            <input type="text" name="search" placeholder="Search tasks..."
                                   value="{{ request('search') }}"
                                   class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md border">
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
                        <a href="{{ route('tasks.kanban') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pending Column -->
                <div class="flex flex-col h-full">
                    <h3 class="font-bold text-lg bg-gray-200 p-3 rounded-t-lg flex justify-between items-center">
                        <span>Pending</span>
                        <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full">{{ count($pendingTasks) }}</span>
                    </h3>
                    <div
                        class="flex-1 bg-gray-100 p-2 rounded-b-lg overflow-y-auto min-h-[500px] max-h-[75vh]"
                        data-status="pending"
                        @drop="dropTask($event, 'pending')"
                        @dragover.prevent="$event.currentTarget.classList.add('bg-gray-200')"
                        @dragleave.prevent="$event.currentTarget.classList.remove('bg-gray-200')"
                        @dragenter.prevent
                    >
                        @forelse($pendingTasks as $task)
                            <div
                                class="bg-white p-3 rounded shadow mb-3 cursor-move border-l-4 {{ $task->isOverdue() ? 'border-red-500' : 'border-gray-500' }}"
                                draggable="true"
                                @dragstart="dragTask($event, {{ $task->id }})"
                                id="task-{{ $task->id }}"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold mb-2">{{ $task->title }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($task->priority === 'high') bg-red-100 text-red-800
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                @if($task->description)
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                    @if($task->due_date)
                                        <span class="{{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                            Due {{ $task->getTimeUntilDue() }}
                                        </span>
                                    @else
                                        <span>No due date</span>
                                    @endif
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-4 rounded shadow text-gray-500 text-center">
                                No pending tasks
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="flex flex-col h-full">
                    <h3 class="font-bold text-lg bg-blue-200 p-3 rounded-t-lg flex justify-between items-center">
                        <span>In Progress</span>
                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ count($inProgressTasks) }}</span>
                    </h3>
                    <div
                        class="flex-1 bg-blue-50 p-2 rounded-b-lg overflow-y-auto min-h-[500px] max-h-[75vh]"
                        data-status="in_progress"
                        @drop="dropTask($event, 'in_progress')"
                        @dragover.prevent="$event.currentTarget.classList.add('bg-blue-100')"
                        @dragleave.prevent="$event.currentTarget.classList.remove('bg-blue-100')"
                        @dragenter.prevent
                    >
                        @forelse($inProgressTasks as $task)
                            <div
                                class="bg-white p-3 rounded shadow mb-3 cursor-move border-l-4 border-blue-500"
                                draggable="true"
                                @dragstart="dragTask($event, {{ $task->id }})"
                                id="task-{{ $task->id }}"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold mb-2">{{ $task->title }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($task->priority === 'high') bg-red-100 text-red-800
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                @if($task->description)
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                    @if($task->due_date)
                                        <span class="{{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                            Due {{ $task->getTimeUntilDue() }}
                                        </span>
                                    @else
                                        <span>No due date</span>
                                    @endif
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-4 rounded shadow text-gray-500 text-center">
                                No in-progress tasks
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Column -->
                <div class="flex flex-col h-full">
                    <h3 class="font-bold text-lg bg-green-200 p-3 rounded-t-lg flex justify-between items-center">
                        <span>Completed</span>
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ count($completedTasks) }}</span>
                    </h3>
                    <div
                        class="flex-1 bg-green-50 p-2 rounded-b-lg overflow-y-auto min-h-[500px] max-h-[75vh]"
                        data-status="completed"
                        @drop="dropTask($event, 'completed')"
                        @dragover.prevent="$event.currentTarget.classList.add('bg-green-100')"
                        @dragleave.prevent="$event.currentTarget.classList.remove('bg-green-100')"
                        @dragenter.prevent
                    >
                        @forelse($completedTasks as $task)
                            <div
                                class="bg-white p-3 rounded shadow mb-3 cursor-move border-l-4 border-green-500"
                                draggable="true"
                                @dragstart="dragTask($event, {{ $task->id }})"
                                id="task-{{ $task->id }}"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold mb-2 line-through text-gray-500">{{ $task->title }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($task->priority === 'high') bg-red-100 text-red-800
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                @if($task->description)
                                    <p class="text-sm text-gray-500 mb-2 line-clamp-2 line-through">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                    @if($task->completed_at)
                                        <span>Completed: {{ $task->completed_at->format('M d') }}</span>
                                    @else
                                        <span>Completed</span>
                                    @endif
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-4 rounded shadow text-gray-500 text-center">
                                No completed tasks
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Hidden form for task status updates -->
            <form method="POST" id="update-task-form" class="hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" id="task-status-input">
                <input type="hidden" name="task_id" id="task-id-input">
            </form>
        </div>
    </div>

    @push('modals')
        <!-- Toast Notification -->
        <div
            x-data="{ show: false, message: '' }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            @task-updated.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg"
        >
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="message"></span>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            function kanbanBoard() {
                return {
                    taskBeingDragged: null,

                    dragTask(event, taskId) {
                        this.taskBeingDragged = taskId;
                        event.dataTransfer.effectAllowed = 'move';
                        const element = document.getElementById(`task-${taskId}`);
                        if (element) {
                            event.dataTransfer.setData('text/plain', taskId);
                        }
                    },

                    dropTask(event, status) {
                        event.preventDefault();

                        if (!this.taskBeingDragged) return;

                        const taskId = this.taskBeingDragged;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const url = `/tasks/${taskId}/update-status`;

                        // Find the empty state message in the target column
                        const emptyStateMessage = event.currentTarget.querySelector('.text-gray-500.text-center');
                        if (emptyStateMessage) {
                            emptyStateMessage.remove();
                        }

                        // Visual update first for better user experience
                        const taskElement = document.getElementById(`task-${taskId}`);

                        if (taskElement) {
                            // Remove empty state message from source column if it will be empty
                            const sourceColumn = taskElement.parentNode;
                            const sourceColumnTasks = sourceColumn.querySelectorAll('[draggable="true"]');
                            if (sourceColumnTasks.length === 1) { // Only the task being moved
                                const noTasksMessage = document.createElement('div');
                                noTasksMessage.className = 'bg-white p-4 rounded shadow text-gray-500 text-center';
                                noTasksMessage.textContent = `No ${sourceColumn.dataset.status || 'pending'} tasks`;
                                sourceColumn.appendChild(noTasksMessage);
                            }

                            // Remove from current column
                            taskElement.parentNode.removeChild(taskElement);

                            // Add to target column
                            event.currentTarget.appendChild(taskElement);

                            // Apply appropriate styling based on new status
                            taskElement.classList.remove('border-gray-500', 'border-blue-500', 'border-green-500');

                            if (status === 'pending') {
                                taskElement.classList.add('border-gray-500');
                                // Remove line-through text if moved back from completed
                                taskElement.querySelector('h4').classList.remove('line-through', 'text-gray-500');
                                if (taskElement.querySelector('p')) {
                                    taskElement.querySelector('p').classList.remove('line-through', 'text-gray-500');
                                }
                            }
                            else if (status === 'in_progress') {
                                taskElement.classList.add('border-blue-500');
                                // Remove line-through text if moved back from completed
                                taskElement.querySelector('h4').classList.remove('line-through', 'text-gray-500');
                                if (taskElement.querySelector('p')) {
                                    taskElement.querySelector('p').classList.remove('line-through', 'text-gray-500');
                                }
                            }
                            else if (status === 'completed') {
                                taskElement.classList.add('border-green-500');
                                // Add line-through to title and description
                                taskElement.querySelector('h4').classList.add('line-through', 'text-gray-500');
                                if (taskElement.querySelector('p')) {
                                    taskElement.querySelector('p').classList.add('line-through', 'text-gray-500');
                                }
                            }

                            // Show success message
                            window.dispatchEvent(new CustomEvent('task-updated', {
                                detail: { message: 'Task updated successfully' }
                            }));
                        }

                        // Then send the API request
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                status: status,
                                _method: 'PATCH'
                            })
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Don't show error to user since the visual update already happened
                            // and the background update will happen on page refresh
                        });

                        this.taskBeingDragged = null;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
