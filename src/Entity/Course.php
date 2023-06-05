<?php

namespace App\Entity;

use App\DTO\CourseDTO;
use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;

define('COURSE_TYPE_BUY', 0);
define('COURSE_TYPE_FREE', 1);
define('COURSE_TYPE_RENT', 2);

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $code;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $type;

    /**
     * @ORM\Column(type="float")
     */
    private float $cost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private ?\DateInterval $duration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTypeString(): string
    {
        return static::intTypeToString($this->type);
    }

    public static function intTypeToString(int $type): string
    {
        switch ($type)
        {
            case COURSE_TYPE_BUY:
                return "buy";
            case COURSE_TYPE_FREE:
                return "free";
            case COURSE_TYPE_RENT:
                return "rent";
            default:
                return "";
        }
    }

    public static function stringTypeToInt(string $type): int
    {
        $constName = 'COURSE_TYPE_'.strtoupper($type);
        if (defined($constName)) {
            return constant($constName);
        }

        return COURSE_TYPE_BUY;
    }

    public static function fromDTO(CourseDTO $courseDTO): self
    {
        $newCourse = (new self())
            ->setCode($courseDTO->code)
            ->setName($courseDTO->name)
            ->setCost($courseDTO->price)
            ->setDuration($courseDTO->duration)
        ;

        $newCourse->setType(static::stringTypeToInt($courseDTO->type));

        return $newCourse;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
