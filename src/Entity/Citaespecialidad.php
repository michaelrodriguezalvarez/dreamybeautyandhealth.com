<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Citaespecialidad
 *
 * @ORM\Table(name="citaespecialidad", indexes={@ORM\Index(name="fk_citaespecialidad_cita", columns={"cita"}), @ORM\Index(name="fk_citaespecialidad_especialidad", columns={"especialidad"})})
 * @ORM\Entity
 */
class Citaespecialidad
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
     * @var \Especialidad
     *
     * @ORM\ManyToOne(targetEntity="Especialidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="especialidad", referencedColumnName="id")
     * })
     */
    private $especialidad;

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

    public function getEspecialidad(): ?Especialidad
    {
        return $this->especialidad;
    }

    public function setEspecialidad(?Especialidad $especialidad): self
    {
        $this->especialidad = $especialidad;

        return $this;
    }


}
