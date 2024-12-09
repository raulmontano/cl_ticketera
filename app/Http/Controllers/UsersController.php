<?php

namespace App\Http\Controllers;

use App\User;
use BadChoice\Thrust\Controllers\ThrustController;
use Hash;
use Illuminate\Support\Str;
use BadChoice\Thrust\Facades\Thrust;

class UsersController extends Controller
{
    public function index()
    {
        return (new ThrustController())->index('agent');
        // $users = User::with('teams')->paginate(25);
        // return view('users.index', ['users' => $users]);
    }

    public function delete(User $user)
    {
        try {
            $user->delete();
        } catch (\Exception $e) {
            return back()->withErrors(['delete' => $e->getMessage()]);
        }

        return back()->withMessage(__('thrust::messages.deleted'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        User::create([
            'name'     => request('name'),
            'email'    => request('email'),
            'password' => Hash::make(request('password')),
            'locale'   => env('APP_LOCALE'), //APP_LOCALE=es
            'token'    => Str::random(60),
        ]);

        return back();
    }

    public function impersonate(User $user)
    {
        auth()->loginUsingId($user->id);

        return redirect()->route('tickets.index');
    }
}
