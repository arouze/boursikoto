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

    /**
     * @return array
     * @throws \Exception
     */
    public function getBannedUserTwitterIds() {
        return $this->userRepository->getAllTwittersIds();
    }

    /**
     * @param $id
     * @return bool|mixed
     * @throws \Exception
     */
    public function banUserById($id) {

        // Ignore already banned users.
        if (in_array($id, $this->getBannedUserTwitterIds())) {
            return false;
        }

        try {
            return $this->userRepository->banUserById($id)->getId();
        } catch (\Exception $e) {
            dump($e);
        }
    }
}
