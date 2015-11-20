<?php

namespace SmartSearch\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Serie
 *
 * @ORM\Table(name="series")
 * @ORM\Entity(repositoryClass="SmartSearch\SearchBundle\Entity\SerieRepository")
 */
class Serie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=200, nullable=true)
     */
    private $genre;

    /**
     * @var integer
     *
     * @ORM\Column(name="dateRelease", type="integer")
     */
    private $dateRelease;

    /**
     * @var string
     *
     * @ORM\Column(name="director", type="string", length=200, nullable=true)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="synopsis", type="text", nullable=true)
     */
    private $synopsis;

    /**
     * @var string
     *
     * @ORM\Column(name="actors", type="text", nullable=true)
     */
    private $actors;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=200)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCrawl", type="date")
     */
    private $dateCrawl;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Serie
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set genre
     *
     * @param string $genre
     * @return Serie
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set dateRelease
     *
     * @param integer $dateRelease
     * @return Serie
     */
    public function setDateRelease($dateRelease)
    {
        $this->dateRelease = $dateRelease;

        return $this;
    }

    /**
     * Get dateRelease
     *
     * @return integer 
     */
    public function getDateRelease()
    {
        return $this->dateRelease;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Serie
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set synopsis
     *
     * @param string $synopsis
     * @return Serie
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    /**
     * Get synopsis
     *
     * @return string 
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set actors
     *
     * @param string $actors
     * @return Serie
     */
    public function setActors($actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return string 
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Serie
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set dateCrawl
     *
     * @param \DateTime $dateCrawl
     * @return Serie
     */
    public function setDateCrawl($dateCrawl)
    {
        $this->dateCrawl = $dateCrawl;

        return $this;
    }

    /**
     * Get dateCrawl
     *
     * @return \DateTime 
     */
    public function getDateCrawl()
    {
        return $this->dateCrawl;
    }

    public function getUploadDir()
    {
        return '/docs/series';
    }

    public function getWebPath()
    {
        $date = $this->dateCrawl->format('Y-m-d');
        return $this->getUploadDir().'/'.$date.'/'.$this->slug."/picture.jpg";
    }
}
