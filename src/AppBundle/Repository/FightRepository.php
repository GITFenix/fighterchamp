<?php
/**
 * Created by PhpStorm.
 * User: slk500
 * Date: 16.08.16
 * Time: 15:20
 */

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

class FightRepository extends EntityRepository
{

    public function fightAllOrderBy($tournament)
    {
        $qb = $this->createQueryBuilder('fight')
            ->addOrderBy('fight.position')
            ->andWhere('fight.tournament = :tournament')
            ->setParameter('tournament', $tournament)
        ;

        $query = $qb->getQuery();
        return $query->execute();

    }

    public function fightReadyOrderBy($tournament)
    {
        $qb = $this->createQueryBuilder('fight')
            ->andWhere('fight.ready = :ready')
            ->andWhere('fight.tournament = :tournament')
            ->setParameter('ready', true)
            ->setParameter('tournament', $tournament)
            ->addOrderBy('fight.position')
        ;

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function setAllFightsReady()
    {

        $em = $this->getEntityManager();

        $q = $em->createQuery('update AppBundle:Fight fight set fight.ready = ?1 where fight.ready = ?2')
            ->setParameter(1, true)
            ->setParameter(2, false);

        $q->execute();
    }


    public function findAllUserSignUpTournamnet()
    {
        $qb = $this->createQueryBuilder('fight')
            ->leftJoin('fight.UserOne', 'fc');

        $query = $qb->getQuery();
        return $query->execute();
    }

}