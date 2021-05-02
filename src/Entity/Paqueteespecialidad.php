<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paqueteespecialidad
 *
 * @ORM\Table(name="paqueteespecialidad", indexes={@ORM\Index(name="fk_paqueteespecialidad_paquete", columns={"paquete"}), @ORM\Index(name="fk_paqueteespecialidad_especialidad", columns={"especialidad"})})
 * @ORM\Entity
 */
class Paqueteespecialidad
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
     * @var \Especialidad
     *
     * @ORM\ManyToOne(targetEntity="Especialidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="especialidad", referencedColumnName="id")
     * })
     */
    private $especialidad;

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

    public function getEspecialidad(): ?Especialidad
    {
        return $this->especialidad;
    }

    public function setEspecialidad(?Especialidad $especialidad): self
    {
        $this->especialidad = $especialidad;

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
