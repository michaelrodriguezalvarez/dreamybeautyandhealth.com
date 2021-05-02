<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Citapaquete
 *
 * @ORM\Table(name="citapaquete", indexes={@ORM\Index(name="fk_citapaquete_cita", columns={"cita"}), @ORM\Index(name="fk_citapaquete_paquete", columns={"paquete"})})
 * @ORM\Entity
 */
class Citapaquete
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
     * @var \Cita
     *
     * @ORM\ManyToOne(targetEntity="Cita")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cita", referencedColumnName="id")
     * })
     */
    private $cita;

    /**
     * @var \Paquete
     *
     * @ORM\ManyToOne(targetEntity="Paquete")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paquete", referencedColumnName="id")
     * })
     */
    private $paquete;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCita(): ?Cita
    {
        return $this->cita;
    }

    public function setCita(?Cita $cita): self
    {
        $this->cita = $cita;

        return $this;
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


}
