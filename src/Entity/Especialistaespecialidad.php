<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Especialistaespecialidad
 *
 * @ORM\Table(name="especialistaespecialidad", indexes={@ORM\Index(name="fk_especialistaespecialidad_especialista", columns={"especialista"}), @ORM\Index(name="fk_especialistaespecialidad_especialidad", columns={"especialidad"})})
 * @ORM\Entity
 */
class Especialistaespecialidad
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
     * @var \Especialista
     *
     * @ORM\ManyToOne(targetEntity="Especialista")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="especialista", referencedColumnName="id")
     * })
     */
    private $especialista;

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

    public function getEspecialista(): ?Especialista
    {
        return $this->especialista;
    }

    public function setEspecialista(?Especialista $especialista): self
    {
        $this->especialista = $especialista;

        return $this;
    }


}
