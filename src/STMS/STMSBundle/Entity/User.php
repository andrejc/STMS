<?php

namespace STMS\STMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="email_unique",columns={"email"})})
 * @ORM\Entity(repositoryClass="STMS\STMSBundle\Entity\UserRepository")
 * @UniqueEntity(fields="email", message="The provided email address is already associated with another account")
 */
class User implements UserInterface
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
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank(message="Email must be provided")
     * @Assert\Email(
     *     message = "Invalid email provided"
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255)
     * @Assert\NotBlank(message="Full name must be provided")
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="Password must be provided")
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="preferredWorkingHoursPerDay", type="integer", nullable=true)
     */
    private $preferredWorkingHoursPerDay;


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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set preferredWorkingHoursPerDay
     *
     * @param integer $preferredWorkingHoursPerDay
     * @return User
     */
    public function setPreferredWorkingHoursPerDay($preferredWorkingHoursPerDay)
    {
        $this->preferredWorkingHoursPerDay = $preferredWorkingHoursPerDay;

        return $this;
    }

    /**
     * Get preferredWorkingHoursPerDay
     *
     * @return integer 
     */
    public function getPreferredWorkingHoursPerDay()
    {
        return $this->preferredWorkingHoursPerDay;
    }

    /**
     * Get user's email, which acts as username
     *
     * @return integer
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }
}
