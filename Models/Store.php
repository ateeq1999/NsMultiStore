<?php
namespace Modules\NsMultiStore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    const STATUS_BUILDING       =   'building';
    const STATUS_OPENED         =   'opened';
    const STATUS_CLOSED         =   'closed';
    const STATUS_DISMANTLING    =   'dismantling';

    protected $table    =   'nexopos_stores';

    public function scopeStatus( $query, $status )
    {
        return $query->where( 'status', $status );
    }
}