<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'position' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'phone.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda + atau -.',
        ]);

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('passwordChange', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.different' => 'Password baru harus berbeda dari password saat ini.',
        ]);

        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('profile')->with('success', 'Password berhasil diganti.');
    }
}
