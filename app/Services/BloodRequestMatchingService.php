<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Support\Collection;

class BloodRequestMatchingService
{
    public function getEligibleMatchedDonors(BloodRequest $request): Collection
    {
        if ($request->receiver_id) {
            return collect();
        }

        $compatibleBloodTypes = $this->compatibleDonors($request->blood_type);

        return User::whereIn('blood_type', $compatibleBloodTypes)
            ->where('id', '!=', $request->user_id)
            ->get()
            ->filter(fn($donor) => $donor->isEligible());
    }

    public function compatibleDonors(string $bloodType): array
    {
        $compatibility = [
            'O-' => ['O-'],
            'O+' => ['O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'B-' => ['B-', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
            'AB+' => ['AB+', 'AB-', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-'],
        ];

        return $compatibility[$bloodType] ?? [];
    }
}
