<?php
namespace App\Services;

use App\Events\UserCreatedEvent;
use App\Services\Event\EventDispatcher;

class UserService
{
    public function __construct(
        private EventDispatcher $dispatcher
    ) {}

    public function create(array $data)
    {
        // $user = User::create($data);

        $user = (object) [
            'id' => 14,
            'name' => 'Nahid',
            'email' => 'n@mail.com',
        ];

        $this->dispatcher->dispatch(
            'user_exchange',
            new UserCreatedEvent(
                $user->id,
                $user->name,
                $user->email
            )
        );

        return $user;
    }
}
