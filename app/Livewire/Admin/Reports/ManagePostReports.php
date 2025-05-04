<?php

namespace App\Livewire\Admin\Reports;

use App\Models\PostReport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ManagePostReports extends Component
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
    public function acceptReport(PostReport $report)
    {
        try {
            $postAuthorId = null;

            if ($report->post) {
                $postAuthorId = $report->post->user_id;
                $report->post->delete();
            }

            $report->update([
                'status' => 'accepted',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            session()->flash('message', 'Report accepted and post soft deleted. Redirecting to user...');

            // Dispatch the event BEFORE redirecting
            $this->dispatch('reportProcessed'); // <-- Make sure this happens

            if ($postAuthorId) {
                return $this->redirect(route('admin.users', ['filterUserId' => $postAuthorId]), navigate: true);
            }
            // If no author ID, the dispatch above handles the refresh

        } catch (\Exception $e) {
            Log::error("Error accepting report {$report->id}: " . $e->getMessage());
            session()->flash('error', 'Failed to accept report.');
            // Dispatch even on error? Maybe, depends on desired behavior.
            // $this->dispatch('reportProcessed');
        }
    }

    // Action to Reject a Report
    public function rejectReport(PostReport $report)
    {
        try {
            $report->update([
                'status' => 'rejected',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
            session()->flash('message', 'Report rejected successfully.');
            $this->dispatch('reportProcessed');
        } catch (\Exception $e) {
            Log::error("Error rejecting report {$report->id}: " . $e->getMessage());
            session()->flash('error', 'Failed to reject report.');
        }
    }

    public function render()
    {
        $reports = PostReport::with(['post.user', 'reporter']) // Eager load relationships
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    // Search by reason
                    $q->where('reason', 'like', '%' . $this->search . '%')
                        // Search by post title
                        ->orWhereHas('post', function ($subQ) {
                        $subQ->where('title', 'like', '%' . $this->search . '%');
                    })
                        // Search by reporter name/email
                        ->orWhereHas('reporter', function ($subQ) {
                        $subQ->where('firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('lastname', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.reports.manage-post-reports', [
            'reports' => $reports,
        ]);
    }
}