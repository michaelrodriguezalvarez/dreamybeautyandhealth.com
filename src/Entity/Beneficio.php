<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Beneficio
 *
 * @ORM\Table(name="beneficio", indexes={@ORM\Index(name="fk_beneficio_imagen", columns={"imagen"})})
 * @ORM\Entity
 */
class Beneficio
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var \Imagen
     *
     * @ORM\ManyToOne(targetEntity="Imagen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="imagen", referencedColumnName="id")
     * })
     */
    private $imagen;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getImagen(): ?Imagen
    {
        return $this->imagen;
    }

    public function setImagen(?Imagen $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }


}
