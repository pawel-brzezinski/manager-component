<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use SmartInt\Component\Manager\ManagerInterface;

/**
 * Interface for Doctrine ORM manager implementation.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
interface ORMManagerInterface extends ManagerInterface
{
    /**
     * Get entity namespace.
     *
     * @return null|string
     */
    public function getEntityNamespace(): ?string;

    /**
     * Set entity namespace.
     *
     * @param string $entityNamespace
     *
     * @return ORMManagerInterface
     */
    public function setEntityNamespace(string $entityNamespace);

    /**
     * Get entity manager.
     *
     * @return EntityManagerInterface|null
     */
    public function getEntityManager(): ?EntityManagerInterface;

    /**
     * Set entity manager.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return ORMManagerInterface
     */
    public function setEntityManager(EntityManagerInterface $entityManager);

    /**
     * Get repository for entity namespace.
     *
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository;
}
