<?php

namespace AppBundle\Security;

use AppBundle\Entity\ImportPgn;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ImportPgnVoter extends Voter
{
    const IMPORT = 'import';
    const DELETE = 'delete';

    /** @var AccessDecisionManagerInterface */
    private $decisionMaker;

    public function __construct(AccessDecisionManagerInterface $decisionMaker)
    {
        $this->decisionMaker = $decisionMaker;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::IMPORT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof ImportPgn) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionMaker->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }
        switch ($attribute) {
            case self::IMPORT:
                return $this->canImport($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('Voting ImportPgn failed. This should not happen!');
    }

    private function canImport(ImportPgn $importPgn, User $user)
    {
        return $user->getUuid() === $importPgn->getUser()->getUuid();
    }

    private function canDelete(ImportPgn $importPgn, User $user)
    {
        return $this->canImport($importPgn, $user) and $importPgn->isImported() === false;
    }
}
