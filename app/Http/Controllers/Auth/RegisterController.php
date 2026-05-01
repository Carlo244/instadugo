<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     */
    protected $redirectTo = '/login';

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'contact' => ['required', 'digits:10'],
                'age' => ['required', 'integer', 'min:18'],
                'sex' => ['required', 'in:Male,Female'],
                'blood_type' => ['required', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'address' => ['required', 'string', 'max:500'],
            ],
            [
                'contact.digits' => 'Contact number must be exactly 10 digits (without +63).',
                'age.min' => 'You must be at least 18 years old to register.',
                'sex.in' => 'Please select a valid sex (Male or Female).',
                'blood_type.in' => 'Please select a valid blood type.',
            ],
        );
    }

    /**
     * Create a new user instance after a valid registration.
     */
    /**
     * Create a new user instance after a valid registration.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            Log::error('Failed to send registration verification email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'contact' => $data['contact'],
            'age' => $data['age'],
            'sex' => $data['sex'],
            'blood_type' => $data['blood_type'],
            'address' => $data['address'],
        ]);
    }
}
