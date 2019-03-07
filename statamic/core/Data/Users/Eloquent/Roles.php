<?php

namespace Statamic\Data\Users\Eloquent;

use Illuminate\Support\Facades\DB;
use Statamic\Contracts\Data\Users\User as UserContract;

class Roles
{
    /**
     * @var UserContract
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }

    /**
     * Get all the role pivot table records for a user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $roles = collect($this->table()->where('user_id', $this->user->id())->get());

        if ($roles->isEmpty()) {
            return collect();
        }

        return $roles; // todo: groups
    }

    /**
     * Sync up the user/role relationship in the pivot table.
     *
     * @param  array $roles  Array of role IDs the user should belong to.
     * @return void
     */
    public function sync($roles)
    {
        if (empty($roles)) {
            return;
        }

        $dbRoles = collect(
            $this->table()->where('user_id', $this->user->id())->get()
        )->keyBy('role_id');

        // Remove roles that exist in the DB that should no longer be there.
        foreach ($dbRoles as $role) {
            if (! $roles->contains($role->role_id)) {
                $this->table()->where('id', $role->id)->delete();
            }
        }

        // Add roles that aren't in the DB that should be there.
        foreach ($roles as $role) {
            if (! $dbRoles->has($role)) {
                $this->table()->insert(['user_id' => $this->user->id(), 'role_id' => $role]);
            }
        }
    }

    /**
     * Get an instance of the query builder from the appropriate table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function table()
    {
        return DB::table('user_roles');
    }
}
