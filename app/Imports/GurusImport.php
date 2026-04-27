<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Spatie\Permission\Models\Role;

class GurusImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures, Importable;

    protected $updatedCount = 0;
    protected $createdCount = 0;
    protected $skippedCount = 0;

    public function model(array $row)
    {
        $id       = $row['id'] ?? null;
        $name     = $row['nama'] ?? null;
        $email    = $row['email'] ?? null;
        $password = $row['password'] ?? null;

        if (!$name || !$email) {
            $this->skippedCount++;
            return null;
        }

        // Try to find existing user by ID
        if ($id) {
            $user = User::find($id);
            if ($user) {
                $data = [
                    'name'  => $name,
                    'email' => $email,
                ];
                if ($password) {
                    $data['password'] = bcrypt($password);
                }
                $user->update($data);
                $this->updatedCount++;
                return null;
            }
        }

        // Check if email already exists
        $existing = User::where('email', $email)->first();
        if ($existing) {
            $data = ['name' => $name];
            if ($password) {
                $data['password'] = bcrypt($password);
            }
            $existing->update($data);
            $this->updatedCount++;
            return null;
        }

        // Create new guru
        if (!$password) {
            $this->skippedCount++;
            return null; // New guru must have a password
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password),
        ]);

        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $user->assignRole($guruRole);

        $this->createdCount++;
        return null; // Already created manually
    }

    public function rules(): array
    {
        return [
            'nama'  => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ];
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
