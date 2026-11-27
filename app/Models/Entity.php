<?php

namespace App\Models;

use Database\Factories\EntityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
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
        'logo_path',
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

    public function getLogoUrlAttribute(): string
    {
        if (filled($this->logo_path)) {
            if (Storage::disk('public')->exists($this->logo_path)) {
                return asset('storage/' . $this->logo_path);
            }

            if (file_exists(public_path($this->logo_path))) {
                return asset($this->logo_path);
            }
        }

        if ($this->hasMedia('logo')) {
            return $this->getFirstMediaUrl('logo');
        }

        return asset($this->defaultStaticLogoRelativePath());
    }

    public function getLogoAbsolutePathAttribute(): string
    {
        if (filled($this->logo_path)) {
            if (Storage::disk('public')->exists($this->logo_path)) {
                return Storage::disk('public')->path($this->logo_path);
            }

            if (file_exists(public_path($this->logo_path))) {
                return public_path($this->logo_path);
            }
        }

        if ($this->hasMedia('logo')) {
            return $this->getFirstMediaPath('logo');
        }

        return public_path($this->defaultStaticLogoRelativePath());
    }

    protected function defaultStaticLogoRelativePath(): string
    {
        $code = strtolower(str_replace(['_', ' '], '-', (string) $this->code));

        return match ($code) {
            'stc' => 'images/entities/km100-stc.jpg',
            'km100-petc', 'petc' => 'images/entities/km100-petc.jpg',
            'knoah', 'knoahmari', 'knoah-mari' => 'images/entities/knoah-mari.jpg',
            'redgoose', 'red-goose' => 'images/entities/red-goose.jpg',
            default => 'images/km100-logo.jpg',
        };
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
