@if (isset($donationHistory))
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-sm" data-bs-toggle="pill" data-bs-target="#don-tab-upcoming"
                type="button">Upcoming</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" data-bs-toggle="pill" data-bs-target="#don-tab-history"
                type="button">History</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="don-tab-upcoming">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Date</th>
                            <th>Hospital</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                            <tr>
                                <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                                <td class="small">{{ $donation->hospitalAdmin->hospital_name }}</td>
                                <td>
                                    <span
                                        class="badge rounded-pill bg-{{ $donation->status == 'scheduled' ? 'info' : ($donation->status == 'completed' ? 'success' : 'danger') }}">{{ ucfirst($donation->status) }}</span>
                                </td>
                                <td class="text-end">
                                    @if ($donation->status == 'scheduled')
                                        <form action="{{ route('user.donations.cancel', $donation->id) }}"
                                            method="POST" onsubmit="return confirm('Cancel this schedule?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm btn-link text-danger p-0">Cancel</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No upcoming donations.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="don-tab-history">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Date</th>
                            <th>Hospital</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donationHistory as $donation)
                            <tr>
                                <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                                <td class="small">{{ $donation->hospitalAdmin->hospital_name }}</td>
                                <td>
                                    <span
                                        class="badge rounded-pill bg-{{ $donation->status == 'completed' ? 'success' : 'danger' }}">{{ ucfirst($donation->status) }}</span>
                                </td>
                                <td class="text-end"> <span class="text-muted small">--</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No donation history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Date</th>
                    <th>Hospital</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                    <tr>
                        <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                        <td class="small">{{ $donation->hospitalAdmin->hospital_name }}</td>
                        <td>
                            <span
                                class="badge rounded-pill bg-{{ $donation->status == 'scheduled' ? 'info' : ($donation->status == 'completed' ? 'success' : 'danger') }}">{{ ucfirst($donation->status) }}</span>
                        </td>
                        <td class="text-end">
                            @if ($donation->status == 'scheduled')
                                <form action="{{ route('user.donations.cancel', $donation->id) }}" method="POST"
                                    onsubmit="return confirm('Cancel this schedule?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0">Cancel</button>
                                </form>
                            @else
                                <span class="text-muted small">--</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No donation history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
