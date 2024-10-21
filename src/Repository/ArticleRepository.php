<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function searchArticles(ArrayCollection $tags)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.tags', 't')
            ->where('t IN (:tags)')
            ->setParameter('tags', $tags)
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();
    }
}
