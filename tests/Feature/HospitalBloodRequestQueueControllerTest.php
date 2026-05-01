<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\HospitalAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HospitalBloodRequestQueueControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_hospital_requests_index_returns_multilevel_queues_for_active_statuses_only(): void
    {
        $hospitalA = HospitalAdmin::create([
            'hospital_name' => 'EAC Cavite Hospital',
            'email' => 'hospital-a@example.com',
            'password' => Hash::make('password'),
            'contact' => '09123456789',
            'address' => 'Cavite City',
            'phlebotomist_count' => 2,
        ]);

        $hospitalB = HospitalAdmin::create([
            'hospital_name' => 'Other Hospital',
            'email' => 'hospital-b@example.com',
            'password' => Hash::make('password'),
            'contact' => '09999888777',
            'address' => 'Dasmarinas',
            'phlebotomist_count' => 1,
        ]);

        $requester = $this->createUser('requester@example.com', 'O+');

        // Active queue entries for hospital A
        $this->createBloodRequest($requester->id, $hospitalA->id, 'Emergency', 'pending');
        $this->createBloodRequest($requester->id, $hospitalA->id, 'Emergency', 'accepted');
        $this->createBloodRequest($requester->id, $hospitalA->id, 'High', 'pending');
        $this->createBloodRequest($requester->id, $hospitalA->id, 'Normal', 'accepted');

        // History entries for hospital A
        $this->createBloodRequest($requester->id, $hospitalA->id, 'Emergency', 'fulfilled');
        $this->createBloodRequest($requester->id, $hospitalA->id, 'High', 'declined');
        $this->createBloodRequest($requester->id, $hospitalA->id, 'Normal', 'cancelled');

        // Should be excluded (different hospital)
        $this->createBloodRequest($requester->id, $hospitalB->id, 'Emergency', 'pending');

        $response = $this
            ->actingAs($hospitalA, 'hospital_admin')
            ->get(route('hospital.requests'));

        $response->assertOk();
        $response->assertViewIs('hospital.requests');
        $response->assertViewHas('priorityOrder', ['Emergency', 'High', 'Normal']);
        $response->assertViewHas('totalActive', 4);

        $response->assertViewHas('queues', function (array $queues): bool {
            if (!isset($queues['Emergency'], $queues['High'], $queues['Normal'])) {
                return false;
            }

            if ($queues['Emergency']['count'] !== 2 || $queues['High']['count'] !== 1 || $queues['Normal']['count'] !== 1) {
                return false;
            }

            $allActive = collect([$queues['Emergency']['requests'], $queues['High']['requests'], $queues['Normal']['requests']])
                ->flatten(1);

            return $allActive->every(function ($req): bool {
                return in_array($req->status, ['pending', 'accepted'], true)
                    && $req->hospital_admin_id !== null;
            });
        });

        $response->assertViewHas('fulfilledRequests', function ($history): bool {
            if ($history->count() !== 3) {
                return false;
            }

            return $history->every(fn ($req): bool => in_array($req->status, ['fulfilled', 'declined', 'cancelled'], true));
        });
    }

    private function createUser(string $email, string $bloodType): User
    {
        return User::factory()->create([
            'email' => $email,
            'contact' => '09120000000',
            'age' => 24,
            'sex' => 'Male',
            'blood_type' => $bloodType,
            'address' => 'Cavite City',
        ]);
    }

    private function createBloodRequest(int $userId, int $hospitalId, string $urgency, string $status): BloodRequest
    {
        return BloodRequest::create([
            'user_id' => $userId,
            'hospital_admin_id' => $hospitalId,
            'blood_type' => 'O+',
            'quantity' => 1,
            'urgency' => $urgency,
            'reason' => 'Test case request',
            'status' => $status,
            'date_needed' => now()->toDateString(),
        ]);
    }
}
