<div>
    <!-- Task List -->
    <x-filament::card>
        <x-table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Assigned Person</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->status }}</td>
                        <td>{{ $task->progress }}%</td>
                        <td>{{ $task->assigned_person }}</td>
                        <td>{{ $task->priority }}</td>
                        <td>
                            <!-- Action buttons for edit and delete -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-filament::card>

    <!-- Task Form -->
    <x-filament::form wire:submit.prevent="saveTask">
        <x-filament::grid cols="2" gap="4">
            <x-filament::input wire:model="taskTitle" label="Title" required />
            <x-text-input wire:model="taskDescription" label="Description" required />
            <x-filament::input wire:model="taskStatus" label="Status" required />
            <x-filament::input wire:model="taskProgress" label="Progress (%)" type="number" min="0" max="100" required />
            <x-filament::input wire:model="assignedPerson" label="Assigned Person" required />
            <x-filament::select wire:model="priority" label="Priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </x-filament::select>
        </x-filament::grid>
        <x-filament::button type="submit">Save Task</x-filament::button>
    </x-filament::form>
</div>
