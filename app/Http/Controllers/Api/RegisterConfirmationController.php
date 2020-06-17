<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RegisterConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        User::where('confirmation_token', request('token'))
        ->firstOrFail()
        ->confirm();

        return redirect('/threads')
        ->with(' flash', 'Your account is now confirmed! You may post to the forum.');
    }

  

}
