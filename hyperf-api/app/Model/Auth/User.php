<?php

declare(strict_types=1);

namespace App\Model\Auth;

use App\Model\Laboratory\FriendRelation;
use App\Model\Model;
use App\Task\Laboratory\FriendWsTask;
use Donjan\Permission\Traits\HasRoles;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;

class User extends Model
{
    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected $container;

    use HasRoles;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Definition state enumeration
     */
    const STATUS_ON = 1;
    const STATUS_OFF= 0;

    /**
     * Gender
     */
    const SEX_BY_MALE = 1;
    const SEX_BY_Female = 0;

    /**
     * Obtain user information based on user ID
     * @param $id
     * @return array|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    static function getOneByUid($id)
    {
        if (empty($id)) return [];

        $query = static::query();
        $query = $query->where('id', $id);

        return $query->first();
    }

    /**
     * Surveillance users add events
     * @param Created $event
     */
    public function created(Created $event)
    {
        $currentUser = $event->getModel();
        $userList = User::query()->where('id', '!=', $currentUser['id'])->get()->pluck('id');

        foreach ($userList as $user_id) {
            FriendRelation::insert([
                'uid' => $currentUser['id'],
                'friend_id' => $user_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        //Maintain the relationship between other user friends
        $this->container->get(FriendWsTask::class)->maintainFriendRelation($currentUser);
    }

    /**
     * Surveillance users delete incidents
     * @param Deleted $event
     */
    public function deleted(Deleted $event)
    {
        $currentUser = $event->getModel();
        //Maintain the relationship between other user friends
        $this->container->get(FriendWsTask::class)->deleteContactEvent($currentUser);
    }
}