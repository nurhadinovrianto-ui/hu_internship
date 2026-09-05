<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value', 'label', 'description', 'group',
    ];

    /**
     * Get the value of a setting by its key, with a fallback default.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set/Update the value of a setting by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\Models\Setting
     */
    public static function setValue(string $key, $value, ?string $label = null, ?string $description = null, ?string $group = null)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $updateData = ['value' => $value];
            if ($label !== null) $updateData['label'] = $label;
            if ($description !== null) $updateData['description'] = $description;
            if ($group !== null) $updateData['group'] = $group;
            $setting->update($updateData);
            return $setting;
        }

        return self::create([
            'key' => $key,
            'value' => $value,
            'label' => $label ?: ucwords(str_replace('_', ' ', $key)),
            'description' => $description,
            'group' => $group ?: 'general',
        ]);
    }
}
