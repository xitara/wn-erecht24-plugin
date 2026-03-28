<?php

namespace Xitara\ERecht24\Models;

use Model;

/**
 * Text Model
 *
 * @property int                                            $id
 * @property string|null                                    $name
 * @property string|null                                    $lang
 * @property string|null                                    $text
 * @property int                                            $is_active
 * @property \Illuminate\Support\Carbon|null                $created_at
 * @property \Illuminate\Support\Carbon|null                $updated_at
 * @method static \Winter\Storm\Database\Collection<int, static> all($columns = ['*'])
 * @method static \Winter\Storm\Database\Collection<int, static> get($columns = ['*'])
 * @method static \Winter\Storm\Database\Builder|Text            lists(string $column, string $key = null)
 * @method static \Winter\Storm\Database\Builder|Text            newModelQuery()
 * @method static \Winter\Storm\Database\Builder|Text            newQuery()
 * @method static \Winter\Storm\Database\Builder|Text            orSearchWhere(string $term, string $columns = [], string $mode = 'all')
 * @method static \Winter\Storm\Database\Builder|Text            query()
 * @method static \Winter\Storm\Database\Builder|Text            searchWhere(string $term, string $columns = [], string $mode = 'all')
 * @method static \Winter\Storm\Database\Builder|Text            whereCreatedAt($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereId($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereIsActive($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereLang($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereName($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereText($value)
 * @method static \Winter\Storm\Database\Builder|Text            whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Text extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'xitara_erecht24_texts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
