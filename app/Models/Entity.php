<?php

namespace App\Models;

use Database\Factories\EntityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Entity extends Model implements HasMedia
{
    /** @use HasFactory<EntityFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'tin',
        'sss_employer_number',
        'philhealth_employer_number',
        'pagibig_employer_number',
        'address',
        'contact_number',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tin' => 'encrypted',
            'sss_employer_number' => 'encrypted',
            'philhealth_employer_number' => 'encrypted',
            'pagibig_employer_number' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'code',
                'name',
                'short_name',
                'address',
                'contact_number',
                'email',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function getMaskedTinAttribute(): string
    {
        return self::maskIdentifier($this->tin);
    }

    public static function maskIdentifier(?string $value): string
    {
        if (blank($value)) {
            return '—';
        }

        $clean = trim($value);
        $len = mb_strlen($clean);

        if ($len <= 4) {
            return str_repeat('•', $len);
        }

        return str_repeat('•', max(4, $len - 4)) . mb_substr($clean, -4);
    }
}
