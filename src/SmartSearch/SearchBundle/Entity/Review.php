<?php

namespace SmartSearch\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="reviews")
 * @ORM\Entity(repositoryClass="SmartSearch\SearchBundle\Entity\ReviewRepository")
 */
class Review
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
     * @ORM\Column(name="idReview", type="string", length=200)
     */
    private $idReview;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="nameSerie", type="string", length=200)
     */
    private $nameSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePublished", type="date", nullable=true)
     */
    private $datePublished;

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
     * Set idReview
     *
     * @param string $idReview
     * @return Review
     */
    public function setIdReview($idReview)
    {
        $this->idReview = $idReview;

        return $this;
    }

    /**
     * Get idReview
     *
     * @return string 
     */
    public function getIdReview()
    {
        return $this->idReview;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set nameSerie
     *
     * @param string $nameSerie
     * @return Review
     */
    public function setNameSerie($nameSerie)
    {
        $this->nameSerie = $nameSerie;

        return $this;
    }

    /**
     * Get nameSerie
     *
     * @return string 
     */
    public function getNameSerie()
    {
        return $this->nameSerie;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Review
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set datePublished
     *
     * @param \DateTime $datePublished
     * @return Review
     */
    public function setDatePublished($datePublished)
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Get datePublished
     *
     * @return \DateTime 
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }

    /**
     * Set dateCrawl
     *
     * @param \DateTime $dateCrawl
     * @return Review
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

    public function getFile($date)
    {
        //$date = $this->dateCrawl->format('Y-m-d');
        return $this->getUploadDir().'/'.$date.'/'.$this->idReview.".html";
    }

    public function getUploadDir()
    {
        return 'docs/reviews';
    }
    public function getFormatedCrawlDate()
    {
		return $this->dateCrawl->format('Y-m-d');
	}
}
