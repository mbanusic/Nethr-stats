<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

	public function __construct() {
		$this->middleware('auth');
	}

	public function showUsers() {
		return view('users.show', ['users' => User::all()]);
	}

	public function editUser($id = null) {
		$user = User::firstOrNew([ 'id' => $id]);
		return view('users.edit', compact('user'));
	}

	public function postUser(Request $request, $id = null) {
		$user = User::updateOrCreate(
			['id' => $id],
			[
				'name' => $request->get('name'),
			]
		);
		if (Gate::allows('update-user', $user)) {
			if ($request->get('password')) {
				$user->password = bcrypt($request->get('password'));
			}
			$user->save();
		}
		return redirect('users');
	}

}
