<?php

namespace Sulu\Bundle\ContactBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

/**
 * AccountContactRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountContactRepository extends EntityRepository
{
    public function findByForeignIds($accountId, $contactId)
    {
        try {
            $qb = $this->createQueryBuilder('accountContact')
                ->leftJoin('accountContact.contact', 'contact')
                ->leftJoin('accountContact.account', 'account')
                ->leftJoin('account.mainContact', 'mainContact')
                ->leftJoin('accountContact.position', 'position')
                ->addSelect('position')
                ->addSelect('mainContact')
                ->addSelect('account')
                ->where('account.id = :accountId AND contact.id = :contactId');

            $query = $qb->getQuery();
            $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
            $query->setParameter('accountId', $accountId);
            $query->setParameter('contactId', $contactId);

            return $query->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }
}