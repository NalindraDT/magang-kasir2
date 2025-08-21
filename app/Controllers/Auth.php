<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Google\Client;
use Google\Service\Oauth2; // Tambahkan ini

class Auth extends BaseController
{
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
