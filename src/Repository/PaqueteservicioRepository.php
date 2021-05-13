<?php

namespace App\Repository;

use App\Entity\Paquete;
use App\Entity\Paqueteservicio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paqueteservicio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paqueteservicio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paqueteservicio[]    findAll()
 * @method Paqueteservicio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaqueteservicioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paqueteservicio::class);
    }

    public function obtenerServiciosPaquete(Paquete $paquete): array {
        $servicios = array();

        $paqueteServicios = $this->findBy(array('paquete'=>$paquete));

        foreach ($paqueteServicios as $paqueteServicio){
            array_push($servicios, $paqueteServicio->getServicio());
        }

        return $servicios;
    }
}
