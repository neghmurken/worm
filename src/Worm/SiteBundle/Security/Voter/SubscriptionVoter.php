<?php

namespace Worm\SiteBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Worm\SiteBundle\Entity\Subscription;

class SubscriptionVoter implements VoterInterface
{

    public static $permissions = array(
        'SUBSCRIPTION_WITHDRAW',
        'SUBSCRIPTION_SUBMIT'
    );

    protected $roleHierarchy;

    /**
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return Boolean true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, static::$permissions);
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return Boolean true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Worm\SiteBundle\Entity\Subscription';

        return $class === $supportedClass || is_subclass_of($class, $supportedClass);
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object $object     The object to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return integer either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $permission = array_shift($attributes);

        if (!$this->supportsAttribute($permission)) {
            return static::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        switch ($permission) {
            case 'SUBSCRIPTION_WITHDRAW':
                if (!$this->supportsClass(get_class($object))) {
                    return static::ACCESS_ABSTAIN;
                }

                if (in_array($object->getState(), array(Subscription::STATE_COMPLETE, Subscription::STATE_WITHDRAWN))) {
                    return static::ACCESS_DENIED;
                }

                if (!$this->hasRole($token, 'ROLE_USER') || $object->getUser()->getId() !== $user->getId()) {
                    return static::ACCESS_DENIED;
                }

                return static::ACCESS_GRANTED;

            case 'SUBSCRIPTION_SUBMIT':
                if (!$this->supportsClass(get_class($object))) {
                    return static::ACCESS_ABSTAIN;
                }

                if (!$this->hasRole($token, 'ROLE_USER') ||
                    $object->getState() !== Subscription::STATE_CURRENT ||
                    $object->getUser()->getId() !== $user->getId()
                ) {
                    return static::ACCESS_DENIED;
                }

                return static::ACCESS_GRANTED;

            default:
                return static::ACCESS_ABSTAIN;
        }
    }

    /**
     * @param TokenInterface $token
     * @param $targetRole
     * @return bool
     */
    protected function hasRole(TokenInterface $token, $targetRole)
    {
        $reachableRoles = $this->roleHierarchy->getReachableRoles($token->getRoles());

        foreach ($reachableRoles as $role) {
            if ($role->getRole() === $targetRole) {
                return true;
            }
        }

        return false;
    }

}