<?php
/**
 * Created by PhpStorm.
 * User: slk500
 * Date: 10.08.16
 * Time: 19:55
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findAllUsersWithFights()
    {
        $qb = $this->createQueryBuilder('user')
            ->leftJoin('user.signUpTournament', 'signuptournament')
            ->leftJoin('signuptournament.fights', 'fights')
            ->addSelect('signuptournament')
            ->addSelect('fights');

          $query = $qb->getQuery();
          return $query->execute();
    }

    public function findAllSignUpButNotPairYet($tournament)
    {
        $qb = $this->createQueryBuilder('user')
          //  ->select('user, signUpTournament.formula')
            ->leftJoin('user.signUpTournament', 'signUpTournament')
            ->andWhere('signUpTournament.user is not null')
            ->andWhere('signUpTournament.ready = 1')
            ->andWhere('signUpTournament.tournament = :tournament')
            ->setParameter('tournament', $tournament)
            ->andWhere('user.fights is empty' )
            ->addOrderBy('user.male')
            ->addOrderBy('signUpTournament.formula')
            ->addOrderBy('signUpTournament.weight')

        ;

      //  $query = $qb->getQuery();
      //  return $query->execute();

        return $qb;
    }
}