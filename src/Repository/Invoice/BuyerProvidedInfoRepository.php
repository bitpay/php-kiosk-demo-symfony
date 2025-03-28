<?php

namespace App\Repository\Invoice;

use App\Entity\Invoice\BuyerProvidedInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuyerProvidedInfo>
 *
 * @method BuyerProvidedInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuyerProvidedInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuyerProvidedInfo[]    findAll()
 * @method BuyerProvidedInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuyerProvidedInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuyerProvidedInfo::class);
    }

    public function save(BuyerProvidedInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BuyerProvidedInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
