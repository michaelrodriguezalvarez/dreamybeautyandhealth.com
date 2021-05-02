<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Especialista
 *
 * @ORM\Table(name="especialista", indexes={@ORM\Index(name="fk_especialista_usuario", columns={"usuario"}), @ORM\Index(name="fk_especialista_foto", columns={"foto"})})
 * @ORM\Entity
 */
class Especialista
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
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=false)
     */
    private $apellidos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="especialidad", type="string", length=255, nullable=true)
     */
    private $especialidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nacionalidad", type="string", length=255, nullable=true)
     */
    private $nacionalidad;

    /**
     * @var \Imagen
     *
     * @ORM\ManyToOne(targetEntity="Imagen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="foto", referencedColumnName="id")
     * })
     */
    private $foto;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     * })
     */
    private $usuario;

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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getEspecialidad(): ?string
    {
        return $this->especialidad;
    }

    public function setEspecialidad(?string $especialidad): self
    {
        $this->especialidad = $especialidad;

        return $this;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(?string $nacionalidad): self
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getFoto(): ?Imagen
    {
        return $this->foto;
    }

    public function setFoto(?Imagen $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }


}
