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
            $postAuthorId = null; // Initialize variable

            if ($report->post) { // Check if post still exists
                $postAuthorId = $report->post->user_id; // Get the author ID BEFORE deleting
                $report->post->delete(); // Soft delete the post
            }

            // Update report status AFTER potential post deletion
            $report->update([
                'status' => 'accepted',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            session()->flash('message', 'Report accepted and post soft deleted. Redirecting to user...');

            // Check if we got an author ID
            if ($postAuthorId) {
                // Redirect to the admin users page, filtering by the post author's ID
                // Use navigate: true for SPA navigation
                return $this->redirect(route('admin.users', ['filterUserId' => $postAuthorId]), navigate: true);
            } else {
                // If post was already deleted or author unknown, just refresh the reports list
                $this->dispatch('reportProcessed');
            }

        } catch (\Exception $e) {
            Log::error("Error accepting report {$report->id}: " . $e->getMessage());
            session()->flash('error', 'Failed to accept report.');
            // Refresh even on error to show potential status changes
            $this->dispatch('reportProcessed');
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