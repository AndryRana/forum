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
        try {
            User::where('confirmation_token', request('token'))
            ->firstOrFail()
            ->confirm();
        } catch (\Exception $e) {
            return redirect('/threads')
            ->with('flash', 'Unknown token.');
        }

        return redirect('/threads')
        ->with('flash', 'Your account is now confirmed! You may post to the forum.');
    }

  

}
