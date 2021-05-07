<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paqueteservicio
 *
 * @ORM\Table(name="paqueteservicio", indexes={@ORM\Index(name="fk_paqueteservicio_paquete", columns={"paquete"}), @ORM\Index(name="fk_paqueteservicio_servicio", columns={"servicio"})})
 * @ORM\Entity
 */
class Paqueteservicio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Paquete
     *
     * @ORM\ManyToOne(targetEntity="Paquete")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paquete", referencedColumnName="id")
     * })
     */
    private $paquete;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="servicio", referencedColumnName="id")
     * })
     */
    private $servicio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaquete(): ?Paquete
    {
        return $this->paquete;
    }

    public function setPaquete(?Paquete $paquete): self
    {
        $this->paquete = $paquete;

        return $this;
    }

    public function getServicio(): ?Servicio
    {
        return $this->servicio;
    }

    public function setServicio(?Servicio $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }


}
