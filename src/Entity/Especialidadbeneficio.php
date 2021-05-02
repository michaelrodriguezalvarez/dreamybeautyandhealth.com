<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Especialidadbeneficio
 *
 * @ORM\Table(name="especialidadbeneficio", indexes={@ORM\Index(name="fk_especialidadbeneficio_especialidad", columns={"especialidad"}), @ORM\Index(name="fk_especialidadbeneficio_beneficio", columns={"beneficio"})})
 * @ORM\Entity
 */
class Especialidadbeneficio
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
     * @var \Beneficio
     *
     * @ORM\ManyToOne(targetEntity="Beneficio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beneficio", referencedColumnName="id")
     * })
     */
    private $beneficio;

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

    public function getBeneficio(): ?Beneficio
    {
        return $this->beneficio;
    }

    public function setBeneficio(?Beneficio $beneficio): self
    {
        $this->beneficio = $beneficio;

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
