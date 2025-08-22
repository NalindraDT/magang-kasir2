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
    public function register()
{
    // Cek jika user sudah login, redirect ke dashboard
    if (session()->get('user_logged_in')) {
        return redirect()->to(base_url('admin'));
    }
    return view('auth/register');
}

public function create()
{
    // Validasi data input
    $rules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'pass_confirm' => 'required|matches[password]',
    ];

    // Validasi reCAPTCHA
    $recaptcha_response = $this->request->getPost('g-recaptcha-response');
    $secretKey = getenv('recaptcha.secretkey');
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $secretKey,
        'response' => $recaptcha_response
    ];
    
    $client = service('curlrequest');
    $response = $client->post($verifyUrl, ['form_params' => $recaptcha_data]);
    $result = json_decode($response->getBody());

    if (!$this->validate($rules) || !$result->success) {
        // Jika validasi gagal, kembalikan ke form
        $errors = $this->validator->getErrors();
        if (!$result->success) {
            $errors['recaptcha'] = 'Verifikasi captcha gagal. Silakan coba lagi.';
        }
        session()->setFlashdata('errors', $errors);
        return redirect()->back()->withInput();
    }

    // Jika semua validasi berhasil, simpan pengguna baru
    $data = [
        'username' => $this->request->getPost('username'),
        'email'    => $this->request->getPost('email'),
        'password' => $this->request->getPost('password'),
    ];
    
    $this->userModel->save($data);

    session()->setFlashdata('message', 'Registrasi berhasil! Silakan login.');
    return redirect()->to(base_url('auth/login'));
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

            $google_service = new Oauth2($client);
            $data = $google_service->userinfo->get();

            // Ambil daftar email yang diizinkan dari .env
            $allowedEmails = explode(',', getenv('google.allowedEmails'));
            
            // Cek apakah email pengguna ada di daftar yang diizinkan
            if (in_array($data->email, $allowedEmails)) {
                // Jika email diizinkan, simpan data ke session
                session()->set('user_logged_in', true);
                session()->set('user_email', $data->email);
                session()->set('user_name', $data->name);
                session()->set('user_picture', $data->picture);

                // Arahkan ke dashboard admin
                return redirect()->to(base_url('admin'));
            }
            
            // Jika email tidak diizinkan, berikan pesan error
            session()->setFlashdata('error', 'Email Anda tidak memiliki izin untuk mengakses halaman ini.');
            return redirect()->to(base_url('auth/login'));
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
