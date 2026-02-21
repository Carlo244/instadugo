@extends('layouts.hospital')

@section('content')
    <main class="content-area">
        <h3 class="mb-4">Multilevel Queue: Blood Requests</h3>

        @forelse($queues as $level => $requests)
            <div class="card mb-4 shadow-sm border-0">

                <div
                    class="card-header 
                @if ($level == 'Emergency') bg-danger text-white 
                @elseif($level == 'High') bg-warning text-dark 
                @else bg-success text-white @endif">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i> {{ $level }} Priority Queue
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date Requested</th>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Urgency</th>
                                    <th>Date Needed</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $request->created_at->format('M d, Y') }}</td>

                                        <td class="fw-semibold text-danger">
                                            {{ $request->blood_type }}
                                        </td>

                                        <td>{{ $request->quantity }}</td>

                                        <td>
                                            <span
                                                class="badge
                                            {{ $request->urgency == 'Emergency'
                                                ? 'bg-danger'
                                                : ($request->urgency == 'High'
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-secondary') }}">
                                                {{ $request->urgency }}
                                            </span>
                                        </td>

                                        <td>{{ \Carbon\Carbon::parse($request->date_needed)->format('M d, Y') }}</td>

                                        <td>
                                            <span
                                                class="badge
                                            {{ $request->status == 'pending'
                                                ? 'bg-warning text-dark'
                                                : ($request->status == 'approved'
                                                    ? 'bg-info'
                                                    : ($request->status == 'fulfilled'
                                                        ? 'bg-success'
                                                        : 'bg-danger')) }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($request->status == 'pending')
                                                <form method="POST"
                                                    action="{{ url('/hospital/requests/' . $request->id . '/approve') }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-primary">Approve</button>
                                                </form>
                                            @endif

                                            @if ($request->status == 'approved')
                                                <form method="POST"
                                                    action="{{ url('/hospital/requests/' . $request->id . '/fulfill') }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success">Fulfill</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-3 text-muted">
                                            No {{ strtolower($level) }} requests in queue.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                All queues are currently empty.
            </div>
        @endforelse
    </main>
@endsection
