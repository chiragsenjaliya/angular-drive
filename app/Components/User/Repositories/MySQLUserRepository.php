<?php

namespace App\Components\User\Repositories;


use App\Components\Core\Result;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Models\User;
use App\Components\Core\Utilities\Helpers;
use Hash;

class MySQLUserRepository implements IUserRepository
{
    /**
     * list all users
     *
     * @param array $params
     * @return Result
     */
    public function listUsers($params)
    {
        $email = Helpers::hasValue($params['email']);
        $name = Helpers::hasValue($params['name']);
        $orderBy = Helpers::hasValue($params['order_by'],'id');
        $orderSort = Helpers::hasValue($params['order_sort'],'desc');
        $paginate = Helpers::hasValue($params['paginate'],'yes');
        $perPage = Helpers::hasValue($params['per_page'],10);
        $groupId = Helpers::hasValue($params['group_id'],"");

        $q = User::with(['groups'])->orderBy($orderBy,$orderSort)->ofGroups(Helpers::commasToArray($groupId));

        (!$email) ?: $q = $q->where('email','like',"%{$email}%");
        (!$name) ?: $q = $q->where('name','like',"%{$name}%");

        if($paginate==='yes')
        {
            return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->paginate($perPage));
        }

        return new Result(true,Result::MESSAGE_SUCCESS_LIST,$q->get());
    }

    /**
     * create new user
     *
     * @param array $payload
     *
     * Sample $payload format
     *
     * $payload = [
     *      'name' => '',
     *      'email' => '',
     *      'password' => '',
     *      'permissions' => [
     *          ['key' => 'user.create', 'value' => 1], // 1 for allow, 0 inherit to group, -1 deny
     *          ['key' => 'user.delete', 'value' => -1], // 1 for allow, 0 inherit to group, -1 deny
     *      ],
     *      'active' => null | '2018-03-04 06:17:40',
     *      'activation_key' => {string},
     *      'groups' => [
     *          {(int)groupID} => true|false // true if assign to a group, false if not
     *      ],
     * ];
     *
     * @return Result
     */
    public function create($payload)
    {
        // create the user
        $User = User::create([
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'email' => $payload['email'],
            'is_parent' => $payload['is_parent'],
            'password' => Hash::make($payload['password']), 
            'phone' => $payload['phone']
        ]);

        if(!$User) return new Result(false,'User not found.',null, 404);        

        return new Result(true,'User created.',$User,201);
    }

    /**
     * update user
     *
     * @param int $id
     * @param array $payload
     * @return Result
     */
    public function update($id, $payload)
    {
        $User = User::find($id);

        if(!$User) return new Result(false,Result::MESSAGE_NOT_FOUND,null,404);

        if(!$User->update($payload)) return new Result(false,'Failed to update.',null,400);

        // detach all group first
        $User->groups()->detach();

        // re attach needed
        if(Helpers::hasValue($payload['groups']) && count($payload['groups']) > 0)
        {
            foreach ($payload['groups'] as $groupId => $shouldAttach) {
                if ($shouldAttach) $User->groups()->attach($groupId);
            }
        }

        return new Result(true,'update success',$User,200);
    }

    /**
     * delete a user by id
     *
     * @param int $id
     * @return Result
     */
    public function delete($id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            $User = User::find($id);

            if(!$User)
            {
                return new Result(false,"Failed to delete resource with id: {$id}. Error: ".Result::MESSAGE_NOT_FOUND,null,404);
            };

            $User->groups()->detach();
            $User->delete();
        }

        return new Result(true,Result::MESSAGE_SUCCESS_DELETE,null);
    }

    /**
     * get resource by id
     *
     * @param $id
     * @return Result
     */
    public function get($id)
    {
        $User = User::with(['groups'])->find($id);

        if(!$User) return new Result(false,'user not found',null,404);

        return new Result(true,Result::MESSAGE_SUCCESS,$User,200);
    }
}