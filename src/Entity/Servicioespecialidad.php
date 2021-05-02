<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicioespecialidad
 *
 * @ORM\Table(name="servicioespecialidad", indexes={@ORM\Index(name="fk_servicioespecialidad_servicio", columns={"servicio"}), @ORM\Index(name="fk_servicioespecialidad_especialidad", columns={"especialidad"})})
 * @ORM\Entity
 */
class Servicioespecialidad
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

    public function getEspecialidad(): ?Especialidad
    {
        return $this->especialidad;
    }

    public function setEspecialidad(?Especialidad $especialidad): self
    {
        $this->especialidad = $especialidad;

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
