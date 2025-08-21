<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Google\Client;
use Google\Service\Oauth2;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel(); // Tambahkan ini
    }
    public function manualLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $username)
            ->orWhere('username', $username)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set('user_logged_in', true);
            session()->set('user_email', $user['email']);
            session()->set('user_name', $user['username']);
            session()->setFlashdata('message', 'Login berhasil!');

            return redirect()->to(base_url('admin'));
        }

        // Ubah pesan error di sini
        session()->setFlashdata('error', 'Password atau username salah, silahkan coba lagi');
        return redirect()->back()->withInput();
    }

    public function googleLogin()
    {
        $client = new Client();
        $client->setClientId(getenv('google.clientId'));
        $client->setClientSecret(getenv('google.clientSecret'));
        $client->setRedirectUri(getenv('google.redirectUri'));
        $client->addScope('email');
        $client->addScope('profile');

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $client = new Client();
        $client->setClientId(getenv('google.clientId'));
        $client->setClientSecret(getenv('google.clientSecret'));
        $client->setRedirectUri(getenv('google.redirectUri'));
        $client->addScope('email');
        $client->addScope('profile');

        if ($this->request->getVar('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
            if (!isset($token['error'])) {
                $client->setAccessToken($token);
                session()->set('google_token', $token);

                // Perbaikan: Gunakan namespace yang benar
                $google_service = new Oauth2($client);
                $data = $google_service->userinfo->get();

                // Simpan data pengguna ke session
                session()->set('user_logged_in', true);
                session()->set('user_email', $data->email);
                session()->set('user_name', $data->name);
                session()->set('user_picture', $data->picture);

                // Arahkan ke dashboard admin
                return redirect()->to(base_url('admin'));
            }
        }
        return redirect()->to(base_url());
    }

    public function login()
    {
        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }
}
