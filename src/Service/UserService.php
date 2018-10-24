<?php

namespace App\Service;


use App\Repository\UserRepository;

class UserService
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getBannedUserCount() {
        return $this->userRepository->count(['isBanned' => 1]);
    }

    public function banUserById($id) {
        try {
            $this->userRepository->banUserById($id);
        } catch (\Exception $e) {
            dump($e);
        }
    }
}
