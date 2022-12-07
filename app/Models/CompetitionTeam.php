<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeam extends BaseModel
{
    use HasFactory, HasCompositePrimaryKeyTrait;

    protected $primaryKey = ['competition_id','team_id'];
    
    public $incrementing = false;

    protected $keyType =  'string';

    protected $casts = [
        'team_id' => 'string',
        'competition_id' => 'string',
    ];

    protected $rules = array(
        'competition_id' => 'required|uuid',
        'team_id'  => 'required|uuid',
    );

    protected $fillable = [
        'team_id',
        'competition_id',
        'status'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
