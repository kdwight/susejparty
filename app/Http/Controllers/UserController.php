<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            $query = User::orderBy(request('column'), request('order'))
                ->where('username', 'like', '%' . request('filter') . '%'); //you can chain these with searchable columns

            return UsersResource::collection($query->paginate(request('per_page')));
        }

        return view('admin.users.index');
    }

    public function create()
    {
        return $this->index();
    }

    public function store(UserRequest $request, User $model)
    {
        unset($request['password_confirmation']);

        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());

        return response('User successfully created.');
    }

    public function edit(User $user)
    {
        return $this->index();
    }

    public function update(UserRequest $request, User  $user)
    {
        $user->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$request->get('password') ? '' : 'password'])
        );

        return response('User successfully updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response(['success' => 'User successfully deleted.'], 200);
    }

    public function status(User $user)
    {
        $user->update([
            'status' => request('status')
        ]);

        return response(['success' => 'Status has been updated'], 200);
    }
}
