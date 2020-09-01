<?php

namespace App\Services\Admin;

use App\Repositories\Eloquent\UserSessionRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class UserServices.
 */
class UserService
{
    /** @var UserSessionRepository $userSessionRepo */
    protected $userSessionRepo;

    /** @var $userRepo */
    protected $userRepo;

    /**
     * UserService constructor.
     * @param UserSessionRepository $userSessionRepo
     */
    public function __construct(
        UserSessionRepository $userSessionRepo,
        UserRepository $userRepo
    ) {
        $this->userSessionRepo = $userSessionRepo;
        $this->userRepo        = $userRepo;
    }

    /**
     * creating a user session record
     *
     * @param $email
     * @return mixed
     */
    public function createUserSession($email)
    {
        $data = [
            'email' => $email,
            'session_key' => Str::random(20),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];

        if ($this->userSessionRepo->create($data)) {
            return $data;
        }

        return null;
    }

    /**
     * checking if session is not valid
     *
     * @param $sessionKey
     * @return bool
     */
    public function sessionKeyIsNotValid($sessionKey)
    {
        $sessionKey = $this->getUserSessionData($sessionKey);

        return empty($sessionKey) || $sessionKey['used_flag'] || ($sessionKey['expires_at'] <= date('Y-m-d H:i:s'));
    }

    /**
     * get user session data
     *
     * @param $sessionKey
     * @return mixed
     */
    public function getUserSessionData($sessionKey)
    {
        return $this->userSessionRepo->buildQueryByAttributes(['session_key' => $sessionKey])->first();
    }

    /**
     * create user
     *
     * @param $data
     * @throws \Exception
     */
    public function createUser($data)
    {
        try {
            DB::beginTransaction();
            // update all records in user sessions
            $this->userSessionRepo->update(['used_flag' => true], $data['email'], 'email');
            // create new user
            $this->userRepo->create([
                                        'email'    => $data['email'],
                                        'password' => Hash::make($data['password']),
                                        'name'     => $data['name']
                                    ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $email
     * @param $password
     * @return mixed|null
     */
    public function getUserLogin($email, $password)
    {
        $user = $this->userRepo->findBy('email', $email);
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Change password of user
     *
     * @param $email
     * @param $password
     * @throws \Exception
     */
    public function changePassword($email, $password)
    {
        try {
            DB::beginTransaction();
            $this->userSessionRepo->update(['used_flag' => true], $email, 'email');
            $this->userRepo->update(['password' => Hash::make($password)], $email, 'email');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
