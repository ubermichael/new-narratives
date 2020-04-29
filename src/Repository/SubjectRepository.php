<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * SubjectRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubjectRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Subject::class);
    }

    public function searchQuery($data) {
        $qb = $this->createQueryBuilder('e');
        if (isset($data['q']) && $data['q']) {
            $qb->addSelect('MATCH (e.label, e.description) AGAINST (:q BOOLEAN) as HIDDEN score');
            $qb->andWhere('MATCH (e.label, e.description) AGAINST (:q BOOLEAN) > 0');
            $qb->setParameter('q', $data['q']);
            $qb->orderBy('score', 'desc');
        }
        if (isset($data['source']) && count($data['source']) > 0) {
            $qb->andWhere('e.subjectSource IN (:sources)');
            $qb->setParameter('sources', $data['source']);
            $qb->orderBy('e.label', 'asc');
        }

        return $qb->getQuery();
    }
}