<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\HospitalAdmin;
use App\Models\User;
use App\Notifications\BloodRequestApproved;
use App\Notifications\BloodRequestDeclined;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HospitalBloodRequestActionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_approve_allows_pending_and_creates_audit_and_notification(): void
    {
        Notification::fake();

        $hospital = $this->createHospital('hospital-approve@example.com');
        $user = $this->createUser('user-approve@example.com');
        $request = $this->createBloodRequest($user->id, $hospital->id, 'pending', 'High');

        $response = $this
            ->actingAs($hospital, 'hospital_admin')
            ->post(route('hospital.requests.approve', $request->id));

        $response->assertStatus(302);

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('blood_request_action_audits', [
            'blood_request_id' => $request->id,
            'hospital_admin_id' => $hospital->id,
            'action' => 'approve',
            'from_status' => 'pending',
            'to_status' => 'accepted',
        ]);

        Notification::assertSentTo($user, BloodRequestApproved::class);
    }

    public function test_approve_rejects_terminal_status_and_does_not_create_audit(): void
    {
        Notification::fake();

        $hospital = $this->createHospital('hospital-terminal@example.com');
        $user = $this->createUser('user-terminal@example.com');
        $request = $this->createBloodRequest($user->id, $hospital->id, 'fulfilled', 'Emergency');

        $response = $this
            ->actingAs($hospital, 'hospital_admin')
            ->post(route('hospital.requests.approve', $request->id));

        $response->assertStatus(302);

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'status' => 'fulfilled',
        ]);

        $this->assertDatabaseMissing('blood_request_action_audits', [
            'blood_request_id' => $request->id,
            'action' => 'approve',
        ]);

        Notification::assertNothingSent();
    }

    public function test_decline_sends_declined_notification_and_audit(): void
    {
        Notification::fake();

        $hospital = $this->createHospital('hospital-decline@example.com');
        $user = $this->createUser('user-decline@example.com');
        $request = $this->createBloodRequest($user->id, $hospital->id, 'pending', 'Normal');

        $response = $this
            ->actingAs($hospital, 'hospital_admin')
            ->post(route('hospital.requests.decline', $request->id));

        $response->assertStatus(302);

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'status' => 'declined',
        ]);

        $this->assertDatabaseHas('blood_request_action_audits', [
            'blood_request_id' => $request->id,
            'hospital_admin_id' => $hospital->id,
            'action' => 'decline',
            'from_status' => 'pending',
            'to_status' => 'declined',
        ]);

        Notification::assertSentTo($user, BloodRequestDeclined::class);
    }

    public function test_priority_update_records_urgency_audit(): void
    {
        Event::fake();

        $hospital = $this->createHospital('hospital-priority@example.com');
        $user = $this->createUser('user-priority@example.com');
        $request = $this->createBloodRequest($user->id, $hospital->id, 'accepted', 'Normal');

        $response = $this
            ->actingAs($hospital, 'hospital_admin')
            ->patch(route('hospital.requests.priority', $request->id), [
                'urgency' => 'Emergency',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'urgency' => 'Emergency',
        ]);

        $this->assertDatabaseHas('blood_request_action_audits', [
            'blood_request_id' => $request->id,
            'hospital_admin_id' => $hospital->id,
            'action' => 'priority_update',
            'from_urgency' => 'Normal',
            'to_urgency' => 'Emergency',
        ]);
    }

    public function test_hospital_cannot_decline_request_owned_by_another_hospital(): void
    {
        $hospitalA = $this->createHospital('hospital-owner-a@example.com');
        $hospitalB = $this->createHospital('hospital-owner-b@example.com');
        $user = $this->createUser('user-owner@example.com');

        $request = $this->createBloodRequest($user->id, $hospitalA->id, 'pending', 'High');

        $response = $this
            ->actingAs($hospitalB, 'hospital_admin')
            ->post(route('hospital.requests.decline', $request->id));

        $response->assertForbidden();

        $this->assertDatabaseHas('blood_requests', [
            'id' => $request->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing('blood_request_action_audits', [
            'blood_request_id' => $request->id,
            'action' => 'decline',
        ]);
    }

    private function createHospital(string $email): HospitalAdmin
    {
        return HospitalAdmin::create([
            'hospital_name' => 'Test Hospital',
            'email' => $email,
            'password' => Hash::make('password'),
            'contact' => '09123456789',
            'address' => 'Cavite',
            'phlebotomist_count' => 2,
        ]);
    }

    private function createUser(string $email): User
    {
        return User::factory()->create([
            'email' => $email,
            'contact' => '09120000000',
            'age' => 24,
            'sex' => 'Female',
            'blood_type' => 'O+',
            'address' => 'Cavite City',
        ]);
    }

    private function createBloodRequest(int $userId, int $hospitalId, string $status, string $urgency): BloodRequest
    {
        return BloodRequest::create([
            'user_id' => $userId,
            'hospital_admin_id' => $hospitalId,
            'blood_type' => 'O+',
            'quantity' => 1,
            'urgency' => $urgency,
            'reason' => 'Controller action test',
            'status' => $status,
            'date_needed' => now()->toDateString(),
        ]);
    }
}
