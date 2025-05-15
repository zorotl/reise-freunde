<?php

namespace App\Livewire\Admin\Reports;

// use App\Models\PostReport;
use App\Models\Report;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ManageReports extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending'; // Default to pending reports
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    protected $listeners = [
        'reportProcessed' => '$refresh', // Refresh list after processing
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    // Action to Accept a Report (Soft Delete Post & Redirect)
    public function acceptReport($id)
    {
        $report = Report::with('reportable')->findOrFail($id);

        $authorId = null;

        if ($report->reportable && $report->reportable instanceof \App\Models\Post) {
            $authorId = $report->reportable->user_id;
            $report->reportable->delete(); // soft-delete
        }

        $report->update([
            'status' => 'accepted',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        session()->flash('message', 'Report accepted and post soft deleted.');

        $this->dispatch('reportProcessed');

        if ($authorId) {
            return $this->redirect(route('admin.users', ['filterUserId' => $authorId]), navigate: true);
        }
    }

    // Action to Reject a Report
    public function rejectReport($id)
    {
        $report = Report::findOrFail($id);

        $report->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        session()->flash('message', 'Report rejected.');
        $this->dispatch('reportProcessed');
    }


    public function render()
    {
        $reports = Report::with(['reporter', 'reportable'])
            ->where('reportable_type', Post::class)
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reason', 'like', '%' . $this->search . '%')
                        ->orWhereHasMorph('reportable', [Post::class], function ($postQuery) {
                            $postQuery->where('title', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('reporter', function ($q) {
                            $q->where('firstname', 'like', '%' . $this->search . '%')
                                ->orWhere('lastname', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.reports.manage-reports', [
            'reports' => $reports,
        ]);
    }
}