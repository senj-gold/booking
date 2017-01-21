<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="places",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"sector", "row", "place"})
 * })
 */
class Places
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="10")
     *
     * @ORM\Column(type="smallint")
     */
    private $sector;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="10")
     *
     * @ORM\Column(type="smallint")
     */
    private $row;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="10")
     *
     * @ORM\Column(type="smallint")
     */
    private $place;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sector.
     *
     * @param integer $sector
     *
     * @return Places
     */
    public function setSector($sector)
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get sector.
     *
     * @return integer
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Set row.
     *
     * @param integer $row
     *
     * @return Places
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row.
     *
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set place.
     *
     * @param integer $place
     *
     * @return Places
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return integer
     */
    public function getPlace()
    {
        return $this->place;
    }
}
