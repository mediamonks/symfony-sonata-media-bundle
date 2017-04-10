<?php

namespace MediaMonks\SonataMediaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class MediaRepository extends EntityRepository
{
    /**
     * @param $query
     * @return Query
     */
    public function search($query)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.title LIKE :query')
            ->setParameter('query', $query . '%');

        return $qb->getQuery();
    }
}
