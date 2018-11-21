<?php declare(strict_types=1);

namespace App\Doctrine\Filter;

use App\Entity\Discount;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DiscountTimeFilter extends SQLFilter
{
    /**
     * Не можу переписати __construct класс і отримати $entityManager через помилку.
     *
     * Cannot override final method SQLFilter->__construct(em : \Doctrine\ORM\EntityManagerInterface) less... (Ctrl+F1)
     * Inspection info: Class hierarchy checks: abstract methods implementation, implementing/overriding method is
     * compatibility with super declaration.  All violations result in PHP fatal errors. It's not recommended to
     * disable or suppress this inspection.
     */
    private $entityManager;

    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (Discount::class !== $targetEntity->getName()) {
            return '';
        }

        $conn = $this->getEntityManager()->getConnection();
        $platform = $conn->getDatabasePlatform();
        $columnStart = $targetEntity->getQuotedColumnName('start_time', $platform);
        $columnStop = $targetEntity->getQuotedColumnName('stop_time', $platform);

        $now = $conn->quote(date($platform->getDateTimeFormatString()));
        $addCondSql = "{$now} < {$targetTableAlias}.{$columnStop}";
//        $addCondSql = "{$now} BETWEEN {$targetTableAlias}.{$columnStart} AND {$targetTableAlias}.{$columnStop}";

        return $addCondSql;
    }

    /**
     * @return EntityManagerInterface
     *
     * @throws \ReflectionException
     */
    protected function getEntityManager()
    {
        if (null === $this->entityManager) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}
