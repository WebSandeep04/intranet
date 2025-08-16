@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Edit Entry Type</h4>
                    <a href="{{ route('entry-type.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Entry Types
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('entry-type.update', $entryType->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Entry Type Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $entryType->name) }}" 
                                           placeholder="e.g., Full Day, Half Day, Leave"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="working_hours" class="form-label">Working Hours <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('working_hours') is-invalid @enderror" 
                                               id="working_hours" 
                                               name="working_hours" 
                                               value="{{ old('working_hours', $entryType->working_hours) }}" 
                                               min="0" 
                                               max="24" 
                                               placeholder="8"
                                               required>
                                        <span class="input-group-text">hours</span>
                                    </div>
                                    @error('working_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Enter the number of working hours for this entry type (0-24)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Optional description for this entry type">{{ old('description', $entryType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Optional description to help users understand this entry type
                            </small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-text">
                                    <strong>Created:</strong> {{ $entryType->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-text">
                                    <strong>Last Updated:</strong> {{ $entryType->updated_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('entry-type.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Entry Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-format working hours input
    $('#working_hours').on('input', function() {
        let value = parseInt($(this).val());
        if (value < 0) $(this).val(0);
        if (value > 24) $(this).val(24);
    });
});
</script>
@endpush
