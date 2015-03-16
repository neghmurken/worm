<?php

namespace Worm\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Worm\SiteBundle\Entity\Repository\SubmissionRepository")
 * @ORM\Table(name="ws_submission")
 * @Assert\Callback(methods={"checkDimensions"})
 */
class Submission
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="submissions")
     * @ORM\JoinColumn(name="author_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $author;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     */
    protected $hash;

    /**
     * @ORM\Column(type="string", length=4)
     */
    protected $extension;

    /**
     * @ORM\Column(type="integer")
     */
    protected $size;

    /**
     * @ORM\Column(type="integer")
     */
    protected $width;

    /**
     * @ORM\Column(type="integer")
     */
    protected $height;

    /**
     * @ORM\Column(type="datetime", name="submitted_at")
     */
    protected $submittedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Worm", inversedBy="submissions")
     * @ORM\JoinColumn(name="worm_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Worm
     */
    protected $worm;

    /**
     *
     */
    public function __construct()
    {
        $this->submittedAt = new \DateTime();
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function checkDimensions(ExecutionContextInterface $context)
    {
        if ($this->width !== $this->worm->getWidth()) {
            $context->addViolationAt(
                'width',
                'The image width should be ' . $this->worm->getWidth() . ' pixels'
            );
        }

        if ($this->height !== $this->worm->getHeight()) {
            $context->addViolationAt(
                'height',
                'The image height should be ' . $this->worm->getHeight() . ' pixels'
            );
        }
    }

    /**
     * @param $author
     */
    public function setAuthor($author)
    {

        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {

        return $this->author;
    }

    /**
     * @param $extension
     */
    public function setExtension($extension)
    {

        $this->extension = $extension;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {

        return $this->extension;
    }

    /**
     * @param $hash
     */
    public function setHash($hash)
    {

        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {

        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * @param $size
     */
    public function setSize($size)
    {

        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {

        return $this->size;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return sprintf(
            '%s.%s',
            $this->getHash(),
            $this->getExtension()
        );
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedAt()
    {
        return $this->submittedAt;
    }

    /**
     * @param Worm $worm
     * @return $this
     */
    public function setWorm(Worm $worm = null)
    {
        $this->worm = $worm;

        return $this;
    }

    /**
     * Get worm
     *
     * @return \Worm\SiteBundle\Entity\Worm
     */
    public function getWorm()
    {
        return $this->worm;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }


}