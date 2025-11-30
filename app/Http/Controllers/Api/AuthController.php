<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hakakses;
use App\Models\Menus;
use App\Models\User;
use App\Models\Userrinci;
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
        $rincian = Userrinci::where('id_heder', $user->id)
        ->get();
        if($user->name == 'Programer'){
            $menuItems = Menus::orderBy('urut')
            ->get();
        }else{
            $menuItems = Hakakses::join('admin_menus', 'admin_menus.id', '=', 'hakakses.idmenu')
            ->where('idwarga', $user->id)
            ->orderBy('urut')
            ->get();
        }

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
            'rincian' => $rincian,
        ]);
    }
}
