<?php

namespace TaskPlannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="TaskPlannerBundle\Repository\CategoryRepository")
 */
class Category
{




    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="category")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="category")
     */
    private $task;





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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->task = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \TaskPlannerBundle\Entity\User $user
     * @return Category
     */
    public function setUser(\TaskPlannerBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TaskPlannerBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add task
     *
     * @param \TaskPlannerBundle\Entity\Task $task
     * @return Category
     */
    public function addTask(\TaskPlannerBundle\Entity\Task $task)
    {
        $this->task[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \TaskPlannerBundle\Entity\Task $task
     */
    public function removeTask(\TaskPlannerBundle\Entity\Task $task)
    {
        $this->task->removeElement($task);
    }

    /**
     * Get task
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTask()
    {
        return $this->task;
    }

    public function __toString() {
        return $this->name;
    }
}
