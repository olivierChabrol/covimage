<?php

namespace App\Entity;

use App\Repository\ImageStackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageStackRepository::class)
 */
class ImageStack {   
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity=ImageUnit::class, mappedBy="stack", orphanRemoval=true)
     */
    private $images;

    /**
     * @var array
     */
    private $uploadedFiles;

    public function __construct() {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self {
        $this->quantity = $quantity;

        return $this;
    }
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }
    public function setUploadedFiles(array $files) {
        $this->uploadedFiles = $files;
        return $this;
    }

    /**
     * @return Collection|ImageUnit[]
     */
    public function getImages(): Collection {
        return $this->images;
    }

    public function addImage(ImageUnit $image): self {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setStack($this);
        }
        return $this;
    }

    public function removeImage(ImageUnit $image): self {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getStack() === $this) {
                $image->setStack(null);
            }
        }

        return $this;
    }
}
