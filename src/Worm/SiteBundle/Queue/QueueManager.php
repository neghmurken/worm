<?php

namespace Worm\SiteBundle\Queue;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Worm\SiteBundle\Entity\User;

class QueueManager
{

    protected $queue;

    protected $rootPath;

    protected $objectManager;

    protected $filesystem;

    /**
     * @param $rootPath
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function __construct($rootPath, ObjectManager $manager)
    {
        $this->filesystem = new Filesystem();

        if (!$this->filesystem->exists($rootPath)) {
            $this->filesystem->mkdir($rootPath);
        }

        $this->rootPath      = $rootPath;
        $this->objectManager = $manager;

        $this->init();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->persist();
    }

    /**
     * @param $id
     * @return object
     */
    protected function getUser($id)
    {
        return $this
            ->objectManager
            ->find('WormSiteBundle:User', $id);
    }

    /**
     * @return string
     */
    protected function getQueueFilepath()
    {
        return $this->rootPath . '/queue';
    }

    /**
     *
     */
    protected function init()
    {
        $filepath = $this->getQueueFilepath();

        if (is_file($filepath) && is_readable($filepath)) {
            $this->queue = $this->parse(file_get_contents($filepath));
        } else {
            $this->queue = array();
        }
    }

    /**
     *
     */
    protected function persist()
    {
        file_put_contents(
            $this->getQueueFilepath(),
            $this->dump($this->queue)
        );
    }

    /**
     * @param $content
     * @return array
     */
    protected function parse($content)
    {
        $output = array();

        foreach (explode('/', $content) as $line) {
            if (strlen(trim($line)) === 0) {
                continue;
            }

            list($time, $userId) = explode(':', $line);
            $output[strval($time)] = $userId;
        }

        return $output;
    }

    /**
     * @param $content
     * @return string
     */
    protected function dump($content)
    {
        $output = array();

        foreach ($this->queue as $time => $userId) {
            $output[] = sprintf('%s:%s', $time, $userId);
        }

        return implode('/', $output);
    }

    /**
     * @param \Worm\SiteBundle\Entity\User $user
     */
    public function queue(User $user)
    {
        if ($this->isQueued($user)) {
            $this->queue[strval(time())] = $user->getId();
            ksort($this->queue);
        }
    }

    /**
     * @return object
     */
    public function current()
    {
        $queue = $this->queue;
        $id    = array_shift($queue);

        return $this->getUser($id);
    }

    /**
     *
     */
    public function pop()
    {
        array_shift($this->queue);
    }

    /**
     * @param \Worm\SiteBundle\Entity\User $user
     */
    public function unqueue(User $user)
    {
        if ($this->isQueued($user)) {
            foreach ($this->queue as $timestamp => $id) {
                if ($id === $user->getId()) {
                    unset($this->queue[$timestamp]);
                    break;
                }
            }
        }
    }

    /**
     * @param \Worm\SiteBundle\Entity\User $user
     * @return int|string
     */
    public function locate(User $user)
    {
        if (!$this->isEmpty()) {
            $positions = array_keys($this->queue);

            foreach ($positions as $i => $timestamp) {
                if ($this->queue[$timestamp] === $user->getId()) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * @param \Worm\SiteBundle\Entity\User $user
     * @return bool
     */
    public function isQueued(User $user)
    {
        return $this->locate($user) !== null;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->size() === 0;
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->queue);
    }

    /**
     *
     */
    public function clear()
    {
        $this->queue = array();
    }
}