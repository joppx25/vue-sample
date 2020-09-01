<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Mail\Admin\ForgotPasswordAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\UserConfirmationRequest;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** @var $userService */
    private $userService;
    /**
     * contruct
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * Admin login page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login()
    {
        return view('admin.auth.login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        return redirect()->route('admin.login');
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginSubmit(LoginRequest $request)
    {
        $user = $this->userService->getUserLogin($request->get('email'), $request->get('password'));

        if (!empty($user)) {
            Auth::login($user, (!empty($request->get('remember_me'))));

            return redirect()->route('admin.booking-list');
        } else {
            return redirect()->route('admin.login')->with('danger', 'Wrong email/password inputted.');
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgotPassword()
    {
        return view('admin.auth.forgot_password');
    }

    /**
     * @param ForgotPasswordRequest $request
     * @throws \Exception
     */
    public function forgotPasswordSubmit(ForgotPasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->get('email');
            $result                     = $this->userService->createUserSession($email);
            $result['confirmation_url'] = route('admin.change-password', $result['session_key']);
            // Send mail
            Mail::to($email)->send(new ForgotPasswordAdmin($result));
            DB::commit();

            return redirect()->route('admin.login')->with('success', '正常に送信されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $sessionKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function changePassword($sessionKey)
    {
        // validation if invalid session key, and already expire
        if ($this->userService->sessionKeyIsNotValid($sessionKey)) {
            // redirect to login
            return redirect('/')->with('warning', 'セッションキーが無効/期限切れです。 管理者に支援をリクエストしてください');
        }

        $email = $this->userService->getUserSessionData($sessionKey)->email;

        return view('admin.auth.change_password', compact('email'));
    }

    /**
     * submit change password
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function changePasswordSubmit(ChangePasswordRequest $request)
    {
        $this->userService->changePassword($request->get('email'), $request->get('password'));

        return redirect()->route('admin.login')->with('success', '正常に送信されました。');
    }

    /**
     * confirmation view
     *
     * @param $sessionKey
     */
    public function confirmation($sessionKey)
    {
        // validation if invalid session key, and already expire
        if ($this->userService->sessionKeyIsNotValid($sessionKey)) {
            // redirect to login
            return redirect()->route('admin.login')->with('warning', 'セッションキーが無効/期限切れです。 管理者に支援をリクエストしてください');
        }

        $email = $this->userService->getUserSessionData($sessionKey)->email;

        return view('admin.auth.confirmation', compact('email'));
    }

    /**
     * submission of creation
     *
     * @param UserConfirmationRequest $request
     * @throws \Exception
     */
    public function confirmationSubmit(UserConfirmationRequest $request)
    {
        $this->userService->createUser($request->all());

        return redirect()->route('admin.confirmation.thanks');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmationThanksPage()
    {
        return view('admin.auth.confirmation_thanks');
    }
}
