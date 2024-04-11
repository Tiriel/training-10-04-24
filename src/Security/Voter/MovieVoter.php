<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MovieVoter extends Voter
{
    public const VIEW = 'movie.view';
    public const OWNER = 'movie.owner';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Movie
            && \in_array($attribute, [self::VIEW, self::OWNER]);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->checkView($subject, $user),
            self::OWNER => $this->checkOwner($subject, $user),
            default => false
        };
    }

    protected function checkView(Movie $movie, User $user): bool
    {
        return match ($movie->getRated()) {
            'G' => true,
            'PG', 'PG-13' => $user->getAge() && $user->getAge() >= 13,
            'R', 'NC-17' => $user->getAge() && $user->getAge() >= 17,
            default => false
        };
    }

    protected function checkOwner(Movie $movie, User $user): bool
    {
        return $this->checkView($movie, $user) && $user === $movie->getCreatedBy();
    }
}
