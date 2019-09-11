<?php

namespace Modules\Itrivia\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use Laracasts\Presenter\PresentableTrait;
use Modules\Itrivia\Presenters\TriviaPresenter;

class Trivia extends Model
{
    use Translatable,PresentableTrait;

    protected $table = 'itrivia__trivias';

    protected $presenter = TriviaPresenter::class;

    public $translatedAttributes = [
        'title',
        'description'
    ];
    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'options'
    ];

    protected $fakeColumns = ['options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function userTrivias()
    {
        return $this->hasMany(UserTrivia::class);
    }

    public function rangePoints()
    {
        return $this->hasMany(RangePoint::class);
    }

    public function getOptionsAttribute($value) {
    
        return json_decode($value);
  
    }
  
    public function setOptionsAttribute($value) {
      
        $this->attributes['options'] = json_encode($value);
      
    }


    /**
     * Magic Method modification to allow dynamic relations to other entities.
     * @var $value
     * @var $destination_path
     * @return string
     */
    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.itrivia.config.relations.trivias', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

}