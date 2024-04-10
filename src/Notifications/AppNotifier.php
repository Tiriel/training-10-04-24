<?php

namespace App\Notifications;

use App\Entity\User;
use App\Notifications\Factory\NotificationFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class AppNotifier
{
    public function __construct(
        protected readonly NotifierInterface $notifier,
        /** @var NotificationFactoryInterface[] $factories */
        #[TaggedIterator('app.notification_factory', defaultIndexMethod: 'getIndex')]
        protected iterable $factories,
    )
    {
        $this->factories = $factories instanceof \Traversable ? iterator_to_array($factories) : $factories;
    }

    public function sendNotification(User $user, string $subject): void
    {
        $channel = $user->getPreferredChannel();
        $notification = $this->factories[$channel]->createNotification('New movie!');

        $this->notifier->send($notification, new Recipient($user->getEmail()));
    }
}
