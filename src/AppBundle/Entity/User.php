<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email"},message="Email already taken!")
 */
class User implements UserInterface
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
     * @var string
     * @Assert\Length(
     *     min="3",
     *     minMessage="Your first name should be at least {{ limit }} characters long!"
     * )
     * @ORM\Column(name="username", type="string", length=100, unique=true)
     */
    private $username;

    /**
     * @var string
     * @Assert\Length(
     *     min = 6,
     *     max = 50,
     *     minMessage="Your password must be at least {{ limit }} characters !",
     *     maxMessage="Your password cannot be longer than {{ limit }} characters !"
     * )
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     * @Assert\Length(
     *     min="3",
     *     minMessage="Your first name should be at least {{ limit }} characters long!"
     * )
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\Length(
     *     min="3",
     *     minMessage="Your last name should be at least {{ limit }} characters long!"
     * )
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registrationDate", type="datetime")
     */
    private $registrationDate;

    /**
     * @var float
     *
     * @ORM\Column(name="cash", type="float")
     */
    private $cash;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="owner")
     */
    private $products;


    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id",referencedColumnName="id")})
     */
    private $roles;


    /**
     * @var Cart
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Cart",mappedBy="user")
     * @ORM\JoinColumn(name="cart_id",referencedColumnName="id")
     */
    private $cart;


    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment",mappedBy="owner")
     */
    private $comments;

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }





    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    /**
     * @return Collection
     */
    public function getProducts(){
        return $this->products;
    }


    /**
     * @param Product $product
     * @return User
     */
    public function addProduct(Product $product){
        $this->products[] = $product;
        return $this;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
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
     * Set email
     *
     * @param string $email
     *
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set cash
     *
     * @param float $cash
     *
     * @return User
     */
    public function setCash($cash)
    {
        $this->cash = $cash;

        return $this;
    }

    /**
     * Get cash
     *
     * @return float
     */
    public function getCash()
    {
        return $this->cash;
    }

    public function increaseCash($cash){
        $this->cash += $cash;
    }

    public function decreaseCash($cash){
        $this->cash -= $cash;
    }

    /**
     * @return Role[]|ArrayCollection
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role){
            /** @var Role $role */
            $stringRoles[] = $role->getRole();
        }
        return $stringRoles;
    }

    /**
     * @param $role
     * @return User
     */
    public function addRole($role){
        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isOwner(Product $product){
        return $product->getOwner()->getId() == $this->getId();
    }


    public function isAdmin(){
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isEditor(){
        return in_array('ROLE_EDITOR', $this->getRoles());
    }

    public function removeRoles(){
        foreach ($this->roles as $role){
            if ($role->getName() !== 'ROLE_USER'){
                $this->roles->removeElement($role);
            }
        }
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}

