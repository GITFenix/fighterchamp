<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Tournament;
use Doctrine\ORM\EntityRepository;


class SignUpTournamentRepository extends EntityRepository
{


    public function findAllSignUpsPaidButDeleted($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->andWhere('signUpTournament.tournament = :tournament')
            ->andWhere('signUpTournament.deleted_at is not null')
            ->andWhere('signUpTournament.isPaid = true')
            ->setParameter('tournament', $tournament);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllSignUpsPaid($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament.user', 'user')
            ->andWhere('signUpTournament.tournament = :tournament')
            ->andWhere('signUpTournament.deleted_at is null')
            ->andWhere('signUpTournament.isPaid = true')
            ->setParameter('tournament', $tournament)
            ->addOrderBy('user.surname');

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllSignUpsPaidAndWeightedOrder($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament.user', 'user')
            ->andWhere('signUpTournament.tournament = :tournament')
            ->andWhere('signUpTournament.deleted_at is null')
            ->andWhere('signUpTournament.isPaid = true')
//            ->andWhere('signUpTournament.fights is empty' )
            ->setParameter('tournament', $tournament)
            ->addOrderBy('signUpTournament.weighted')
            ->addOrderBy('user.surname');

        $query = $qb->getQuery();
        return $query->execute();
    }


    public function signUpUserOrder($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament.user', 'user')
            ->addSelect('user')
            ->andWhere('signUpTournament.tournament = :tournament')
            ->andWhere('signUpTournament.deleted_at is null')
            ->setParameter('tournament', $tournament)
            ->addOrderBy('user.male')
            ->addOrderBy('signUpTournament.formula')
            ->addOrderBy('signUpTournament.weight');

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllSignUpButNotPairYet(int $tournamentId)
    {

        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare("SELECT sut.id FROM signuptournament sut
LEFT JOIN user u ON sut.user_id = u.id
WHERE sut.tournament_id = $tournamentId
AND sut.deleted_at IS NULL
AND u.id NOT IN (
SELECT uu.id FROM user uu
JOIN user_fight uf ON uf.user_id = uu.id
JOIN fight f ON uf.fight_id = f.id
WHERE f.tournament_id = $tournamentId)");



        $stmt->execute();

        return $stmt->fetchAll();

    }



    public function findAllSortByMaleClassWeightSurname($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament . user', 'user')
            ->andWhere('signUpTournament . tournament = :tournament')
            ->andWhere('signUpTournament . deleted_at is null')
            ->setParameter('tournament', $tournament)
            ->addSelect('user')
            ->addOrderBy('user . male')
            ->addOrderBy('signUpTournament . formula')
            ->addOrderBy('signUpTournament . weight')
            ->addOrderBy('user . surname')

        ;

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllForTournament($tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament . user', 'user')
            ->andWhere('signUpTournament . tournament = :tournament')
            ->andWhere('signUpTournament . deleted_at is null')
            ->andWhere('signUpTournament.deletedAtByAdmin is null')
            ->setParameter('tournament', $tournament)
            ->addSelect('user')
            ->addOrderBy('signUpTournament . weighted')
            ->addOrderBy('user . surname')
        ;

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findMusicForTournament(Tournament $tournament)
    {
        $qb = $this->createQueryBuilder('signUpTournament')
            ->leftJoin('signUpTournament . user', 'user')
            ->andWhere('signUpTournament . tournament = :tournament')
            ->andWhere('signUpTournament . deleted_at is null')
            ->andWhere('signUpTournament.deletedAtByAdmin is null')
            ->andWhere('signUpTournament.youtubeId is not null')
            ->setParameter('tournament', $tournament)
        ;

        $query = $qb->getQuery();
        return $query->execute();
    }

}