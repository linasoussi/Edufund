<?php

 

namespace App\Http\Controllers\Auth;

 

use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules\Password;

 

class AuthController extends Controller

{

    /**

     * Afficher le formulaire de connexion.

     */

    public function showLogin()

    {

        return view('auth.login');

    }

 

    /**

     * Afficher le formulaire d'inscription.

     */

    public function showRegister()

    {

        return view('auth.register');

    }

 

    /**

     * Traiter la tentative de connexion.

     */

    public function login(Request $request)

    {

        $credentials = $request->validate([

            'email'    => ['required', 'email'],

            'password' => ['required'],

        ]);

 

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));

        }

 

        // Message d'erreur personnalisé (modifiable)

        return back()->withErrors([

            'email' => '❌ Adresse e-mail ou mot de passe incorrect.',

        ])->onlyInput('email');

    }

 

    /**

     * Enregistrer un nouvel utilisateur.

     */

    public function register(Request $request)

    {

        $request->validate([

            'name'            => ['required', 'string', 'max:255'],

            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],

            'password'        => ['required', 'confirmed', Password::min(8)],

            'role'            => ['required', 'in:etudiant,contributeur,partenaire'],

            'phone'           => ['nullable', 'string', 'max:20'],

            // Étudiant

            'university'      => ['nullable', 'string', 'max:255'],

            'field_of_study'  => ['nullable', 'string', 'max:255'],

            // Partenaire

            'company'         => ['nullable', 'string', 'max:255'],

            'website'         => ['nullable', 'url'],

        ]);

 

        $user = User::create([

            'name'            => $request->name,

            'email'           => $request->email,

            'password'        => Hash::make($request->password),

            'role'            => $request->role,

            'phone'           => $request->phone,

            'university'      => $request->university,

            'field_of_study'  => $request->field_of_study,

            'company'         => $request->company,

            'website'         => $request->website,

        ]);

 

        Auth::login($user);

 

        // Message de bienvenue modifié (CoFund au lieu d'EduFund)

        return redirect()->route('dashboard')->with('success', '🎉 Bienvenue sur CoFund !');

    }

 

    /**

     * Déconnecter l'utilisateur.

     */

    public function logout(Request $request)

    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');

    }

}