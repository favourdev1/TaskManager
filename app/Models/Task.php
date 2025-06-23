<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get a human-readable string of how much time until the task is due
     * or how overdue it is.
     */
    public function getTimeUntilDue(): string
    {
        if (!$this->due_date) {
            return 'No due date';
        }

        $now = Carbon::now();
        $due = $this->due_date;

        if ($this->status === 'completed') {
            return 'Completed ' . $this->completed_at->diffForHumans([
                'parts' => 2,
                'join' => true,
                'short' => false,
                'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW
            ]);
        }

        if ($due->isPast()) {
            $diff = $now->diff($due);

            if ($diff->y > 0) {
                return 'Overdue by ' . $diff->y . ' ' . Str::plural('year', $diff->y) .
                       ($diff->m > 0 ? ' and ' . $diff->m . ' ' . Str::plural('month', $diff->m) : '');
            }
            if ($diff->m > 0) {
                return 'Overdue by ' . $diff->m . ' ' . Str::plural('month', $diff->m) .
                       ($diff->d > 0 ? ' and ' . $diff->d . ' ' . Str::plural('day', $diff->d) : '');
            }
            if ($diff->d > 0) {
                return 'Overdue by ' . $diff->d . ' ' . Str::plural('day', $diff->d) .
                       ($diff->h > 0 ? ' and ' . $diff->h . ' ' . Str::plural('hour', $diff->h) : '');
            }
            if ($diff->h > 0) {
                return 'Overdue by ' . $diff->h . ' ' . Str::plural('hour', $diff->h) .
                       ($diff->i > 0 ? ' and ' . $diff->i . ' ' . Str::plural('minute', $diff->i) : '');
            }
            if ($diff->i > 0) {
                return 'Overdue by ' . $diff->i . ' ' . Str::plural('minute', $diff->i);
            }
            return 'Overdue by less than a minute';
        }

        $diff = $due->diff($now);
        if ($diff->y > 0) {
            return 'Due in ' . $diff->y . ' ' . Str::plural('year', $diff->y) .
                   ($diff->m > 0 ? ' and ' . $diff->m . ' ' . Str::plural('month', $diff->m) : '');
        }
        if ($diff->m > 0) {
            return 'Due in ' . $diff->m . ' ' . Str::plural('month', $diff->m) .
                   ($diff->d > 0 ? ' and ' . $diff->d . ' ' . Str::plural('day', $diff->d) : '');
        }
        if ($diff->d > 0) {
            return 'Due in ' . $diff->d . ' ' . Str::plural('day', $diff->d) .
                   ($diff->h > 0 ? ' and ' . $diff->h . ' ' . Str::plural('hour', $diff->h) : '');
        }
        if ($diff->h > 0) {
            return 'Due in ' . $diff->h . ' ' . Str::plural('hour', $diff->h) .
                   ($diff->i > 0 ? ' and ' . $diff->i . ' ' . Str::plural('minute', $diff->i) : '');
        }
        if ($diff->i > 0) {
            return 'Due in ' . $diff->i . ' ' . Str::plural('minute', $diff->i);
        }
        return 'Due in less than a minute';
    }
}
