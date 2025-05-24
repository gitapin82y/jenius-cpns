@extends('layouts.admin')

@section('title', 'CBF Expert Evaluation')

@section('content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Expert Evaluation</h1>
        <a href="{{ route('admin.cbf-evaluation.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if($pendingEvaluations->isEmpty())
        <!-- No Pending Evaluations -->
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4 class="text-gray-800">All Evaluations Complete!</h4>
                <p class="text-muted mb-4">There are no pending recommendations to evaluate at this time.</p>
                <a href="{{ route('admin.cbf-evaluation.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> View Dashboard
                </a>
            </div>
        </div>
    @else
        <!-- Instructions Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Evaluation Instructions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2"><strong>How to Rate Relevance:</strong></p>
                        <ul class="mb-3">
                            <li><strong>5 (Excellent):</strong> Highly relevant and very helpful for the user</li>
                            <li><strong>4 (Good):</strong> Relevant and helpful</li>
                            <li><strong>3 (Fair):</strong> Somewhat relevant</li>
                            <li><strong>2 (Poor):</strong> Slightly relevant but not very helpful</li>
                            <li><strong>1 (Very Poor):</strong> Not relevant at all</li>
                        </ul>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i> 
                            <strong>Note:</strong> Scores of 3 and above are considered "relevant" for CBF performance metrics.
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-primary mb-2">Quick Stats</h6>
                            <p class="mb-1"><strong>Pending:</strong> {{ $pendingEvaluations->total() }}</p>
                            <p class="mb-0"><strong>Current Page:</strong> {{ $pendingEvaluations->currentPage() }} of {{ $pendingEvaluations->lastPage() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation Form -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-check"></i> Pending Evaluations 
                    <span class="badge badge-warning ml-2">{{ $pendingEvaluations->total() }} remaining</span>
                </h6>
            </div>
            <div class="card-body">
                @foreach($pendingEvaluations as $evaluation)
                    <div class="card mb-4 border-left-info">
                        <div class="card-body">
                            <div class="row">
                                <!-- Evaluation Info -->
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <h6 class="text-primary mb-2">
                                            <i class="fas fa-user"></i> User: 
                                            <span class="text-dark">{{ $evaluation->user->name ?? 'N/A' }}</span>
                                        </h6>
                                        <h6 class="text-info mb-2">
                                            <i class="fas fa-book"></i> Material: 
                                            <span class="text-dark">{{ $evaluation->material->title ?? 'N/A' }}</span>
                                        </h6>
                                        
                                        @if($evaluation->material && $evaluation->material->description)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>Description:</strong> 
                                                    {{ Str::limit($evaluation->material->description, 150) }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Recommendation Status</small>
                                                @if($evaluation->is_recommended)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Recommended
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times"></i> Not Recommended
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Similarity Score</small>
                                                <span class="font-weight-bold">
                                                    {{ $evaluation->similarity_score ? number_format($evaluation->similarity_score, 3) : 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Date</small>
                                                <span class="font-weight-bold">
                                                    {{ $evaluation->created_at->format('M d, Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluation Form -->
                                <div class="col-lg-4">
                                    <form class="evaluation-form" data-evaluation-id="{{ $evaluation->id }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label font-weight-bold">Relevance Rating</label>
                                            <div class="rating-buttons">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" 
                                                            class="btn btn-outline-primary rating-btn me-1 mb-1" 
                                                            data-rating="{{ $i }}">
                                                        {{ $i }}
                                                        @if($i == 1) <small>Poor</small>
                                                        @elseif($i == 3) <small>Fair</small>
                                                        @elseif($i == 5) <small>Excellent</small>
                                                        @endif
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="relevance_score" class="relevance-score-input" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Notes (Optional)</label>
                                            <textarea name="notes" 
                                                      class="form-control form-control-sm" 
                                                      rows="3" 
                                                      placeholder="Add evaluation notes..."></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-check"></i> Submit Evaluation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($pendingEvaluations->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pendingEvaluations->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Custom Styles -->
<style>
.rating-buttons .rating-btn {
    min-width: 45px;
    font-size: 0.875rem;
}

.rating-buttons .rating-btn.active {
    background-color: #4e73df !important;
    border-color: #4e73df !important;
    color: white !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.evaluation-form {
    background-color: #f8f9fc;
    padding: 1rem;
    border-radius: 0.35rem;
    border: 1px solid #e3e6f0;
}

.card-body .row:not(:last-child) {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .rating-buttons .rating-btn {
        min-width: 40px;
        font-size: 0.75rem;
        margin-bottom: 0.25rem !important;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating button functionality
    document.querySelectorAll('.rating-btn').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.evaluation-form');
            const allButtons = form.querySelectorAll('.rating-btn');
            const hiddenInput = form.querySelector('.relevance-score-input');
            
            // Remove active class from all buttons in this form
            allButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Set the hidden input value
            hiddenInput.value = this.dataset.rating;
        });
    });

    // Form submission
    document.querySelectorAll('.evaluation-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const evaluationId = this.dataset.evaluationId;
            const relevanceScore = this.querySelector('.relevance-score-input').value;
            const notes = this.querySelector('[name="notes"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!relevanceScore) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rating Required',
                    text: 'Please select a relevance rating before submitting.',
                    confirmButtonColor: '#4e73df'
                });
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            // Submit evaluation
            fetch('{{ route("admin.cbf-evaluation.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    evaluation_id: evaluationId,
                    relevance_score: parseInt(relevanceScore),
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Success - remove the evaluation card
                    const card = this.closest('.card.mb-4');
                    card.style.transition = 'opacity 0.3s ease';
                    card.style.opacity = '0';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Check if there are any remaining evaluations
                        const remainingEvaluations = document.querySelectorAll('.evaluation-form').length;
                        if (remainingEvaluations === 0) {
                            location.reload(); // Reload to show "all complete" message
                        }
                    }, 300);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Evaluation Submitted!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Evaluation';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'There was an error submitting your evaluation. Please try again.',
                    confirmButtonColor: '#4e73df'
                });
            });
        });
    });
});
</script>

@endsection