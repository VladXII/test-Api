<?php

namespace App\Entity\Product;

use App\Entity\Brand\Brand;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Product
 *
 * @ORM\Table(name="product", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"mpn", "brand_id"})})
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 */
class Product
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mpn", type="string", length=255)
     * @JMS\Groups({"api"})
     * @JMS\Expose()
     */
    private $mpn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand\Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"api"})
     * @JMS\Expose()
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Groups({"api"})
     * @JMS\Expose()
     */
    private $name;

    /**
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     */
    public function setMpn(string $mpn): void
    {
        $this->mpn = $mpn;
    }

    /**
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     */
    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
