<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value, string $group = 'general', string $type = 'string', ?string $label = null): self
    {
        $stored = is_array($value) ? json_encode($value) : (string) $value;

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type, 'label' => $label ?? $key]
        );
    }
}
