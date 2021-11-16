<?php

namespace Settings\DatabaseSettings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Settings\Database\Factories\SavedSettingFactory;

class SavedSetting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = ['key', 'value', 'model_id'];

    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->table = config('laravel-settings.table', 'settings');
        parent::__construct($attributes);
    }

    protected static function newFactory()
    {
        return new SavedSettingFactory();
    }

}
