<?php

namespace STMS\STMSBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository {

    public function getTasksWithinDateRange($user, $startDate, $endDate) {
        $mQueryBuilder = $this->getEntityManager()->createQueryBuilder();

        $mQueryBuilder->select('t')
            ->from('STMSBundle:Task', 't')
            ->where('t.user = :user')
            ->setParameter('user', $user);

        if ($startDate !== null) {
            $mQueryBuilder->andWhere('t.date >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            $mQueryBuilder->andWhere('t.date <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        return $mQueryBuilder->getQuery()->getResult();
    }
}