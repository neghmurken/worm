<?php

namespace Worm\SiteBundle\Queue;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;

class QueueManager
{

    protected $rootPath;
    protected $objectManager;
    protected $eventDispatcher;
    protected $filesystem;

    /**
     * @param $rootPath
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct($rootPath, ObjectManager $manager, EventDispatcherInterface $eventDispatcher)
    {
        $this->filesystem = new Filesystem();

        if (!$this->filesystem->exists($rootPath)) {
            $this->filesystem->mkdir($rootPath);
        }

        $this->rootPath        = $rootPath;
        $this->objectManager   = $manager;
        $this->eventDispatcher = $eventDispatcher;
    }
}