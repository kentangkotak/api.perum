<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menus;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $user = auth()->user();

        $menuItems = Menus::orderBy('urut')
        ->get();

        // $menuItems = $menus->map(function ($menu) {
        //     $item = [
        //         'label' => $menu->label,
        //         'icon' => $menu->icon,
        //     ];

        //     if ($menu->submenus->isNotEmpty()) {
        //         $item['children'] = $menu->submenus->map(function ($sub) {
        //             return [
        //                 'label' => $sub->label,
        //                 'icon' => $sub->icon,
        //                 'to' => $sub->link,
        //             ];
        //         });
        //     } else {
        //         $item['to'] = $menu->link ?? '/';
        //     }

        //     return $item;
        // });

        return response()->json([
            'status' => true,
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user,
            'menu' => $menuItems,
        ]);
    }
}
