<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('pengurus')->orderBy('name')->paginate(25);

        return view('ketua-umum.user.index', compact('users'));
    }

    public function create()
    {
        return view('ketua-umum.user.create', [
            'pengurusList' => Pengurus::where('is_active', true)->orderBy('nama')->get(),
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:'.implode(',', array_keys($this->roleOptions())),
            'pengurus_id' => 'nullable|exists:pengurus,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::catat('create', $user, "Membuat akun user {$user->username} dengan role {$user->role}");

        return redirect()->route('ketua-umum.user.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('ketua-umum.user.edit', [
            'user' => $user,
            'pengurusList' => Pengurus::where('is_active', true)->orderBy('nama')->get(),
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:'.implode(',', array_keys($this->roleOptions())),
            'pengurus_id' => 'nullable|exists:pengurus,id',
            'is_active' => 'boolean',
        ]);

        $dataSebelum = $user->only(['name', 'username', 'email', 'role', 'pengurus_id', 'is_active']);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Mencegah Ketua Umum yang sedang login mengubah role dirinya sendiri jadi role lain (mencegah lockout)
        abort_if(
            $user->id === auth()->id() && $validated['role'] !== $user->role,
            422,
            'Tidak bisa mengubah role akun Anda sendiri yang sedang aktif login.'
        );

        $user->update($validated);

        ActivityLog::catat('update', $user, "Mengubah akun user {$user->username}", $dataSebelum, $user->getChanges());

        return redirect()->route('ketua-umum.user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'Tidak bisa menghapus akun Anda sendiri yang sedang aktif login.');

        $dataSebelum = $user->toArray();
        $username = $user->username;
        $user->delete();

        ActivityLog::catat('delete', $user, "Menghapus akun user {$username}", $dataSebelum);

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Role final untuk versi ini (3 role), lihat PRD bagian 3.
     * Menambah role baru di masa depan: tambah entri di sini + di konstanta User::ROLE_*.
     */
    protected function roleOptions(): array
    {
        return [
            User::ROLE_KETUA_UMUM => 'Ketua Umum',
            User::ROLE_SEKRETARIS => 'Sekretaris',
            User::ROLE_BENDAHARA => 'Bendahara',
        ];
    }
}
