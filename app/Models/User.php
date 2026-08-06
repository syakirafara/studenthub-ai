<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function opportunitiesSubmitted()
    {
        return $this->hasMany(Opportunity::class, 'submitted_by');
    }

    public function matches()
    {
        return $this->hasMany(OpportunityMatch::class);
    }

    public function savedItems()
    {
        return $this->hasMany(SavedItem::class);
    }

    /**
     * Peluang yang ditandai simpan, langsung sebagai objek Opportunity.
     *
     * Bedanya dengan savedItems(): yang itu mengembalikan baris penghubung,
     * yang ini langsung peluangnya. Dipakai di halaman Tersimpan supaya bisa
     * disaring dan diurutkan seperti katalog biasa.
     */
    public function peluangTersimpan()
    {
        return $this->belongsToMany(Opportunity::class, 'saved_items')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
