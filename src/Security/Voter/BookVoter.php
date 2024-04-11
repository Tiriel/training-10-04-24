<?php

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookVoter extends Voter
{
    public const CREATED = 'book.created_by';
    public const SHOW = 'book.show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Book
            && \in_array($attribute, [self::SHOW, self::CREATED]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Book $subject */
        return match ($attribute) {
            self::CREATED => $user === $subject->getCreatedBy(),
            default => false,
        };
    }
}
