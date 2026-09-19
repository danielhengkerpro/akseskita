<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Daftarkan user baru (role default: user).
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'string', 'in:disability,contributor'],
            'disability_type' => ['nullable', 'required_if:account_type,disability', 'string', 'in:wheelchair,blind,deaf,elderly,other'],
        ], [
            'disability_type.required_if' => 'Silakan pilih ragam disabilitas yang sesuai untuk personalisasi akunmu.',
        ]);

        $accountType = $validated['account_type'];
        $disabilityType = $accountType === User::ACCOUNT_DISABILITY ? ($validated['disability_type'] ?? null) : null;

        $preferences = [];
        if ($disabilityType && isset(User::DISABILITY_TYPES[$disabilityType])) {
            $preferences['priorities'] = User::DISABILITY_TYPES[$disabilityType]['priorities'];
            if ($disabilityType === 'blind') {
                $preferences['text_scale'] = 125;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_USER,
            'account_type' => $accountType,
            'disability_type' => $disabilityType,
            'accessibility_preferences' => $preferences,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard')
            ->with('status', 'Akun berhasil dibuat. Selamat bergabung di AksesKita!');
    }
}
