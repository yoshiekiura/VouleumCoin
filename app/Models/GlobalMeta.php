<?php
/**
 * GlobalMeta Model
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GlobalMeta Model
 *
 * @since 1.0 @version 1.0
 */
class GlobalMeta extends Model
{
    /*
     * Table Name Specified
     */
    protected $table = 'global_metas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     *
     * Save the meta
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function save_meta($name, $value = null, $pid = null, $extra = null)
    {
        if ($pid != null) {
            $meta = self::where(['name' => $name, 'pid' => $pid])->first();
        } else {
            $meta = self::where(['name' => $name])->first();
        }

        if ($meta == null) {
            $meta = new self();
            $meta->name = $name;
            if ($pid != null) {
                $meta->pid = $pid;
            }
        }

        if ($value != null) {
            $meta->value = $value;
            if ($extra != null) {
                $meta->extra = $extra;
            }
            $meta->save();
        }

        return $meta;
    }

    public static function get_super_admins()
    {
        $sas = self::where('name', 'site_super_admin')->get();
        $users = [];
        foreach ($sas as $user) {
            array_push($users, $user->id);
        }
        return $users;
    }

    /**
     *
     * Relation with Users
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'pid', 'id');
    }

    /**
     *
     * Relation with Json Data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function data()
    {
        return (is_json($this->value) ? json_decode($this->value) : $this->value);
    }
}
