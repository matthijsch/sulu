<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\SearchBundle\Tests\Functional;

use Sulu\Component\Content\Mapper\ContentMapperInterface;
use Sulu\Component\Content\Structure;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase as SymfonyCmfBaseTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Sulu\Bundle\SearchBundle\Tests\Fixtures\DefaultStructureCache;
use Sulu\Component\Content\StructureInterface;

class BaseTestCase extends SymfonyCmfBaseTestCase
{
    public function getKernelConfiguration()
    {
        return array('environment' => 'dev');
    }

    public function setUp()
    {
        $fs = new Filesystem;
        $fs->remove(__DIR__ . '/../Resources/app/data');
    }

    public function getSearchManager()
    {
        $searchManager = $this->getContainer()->get('massive_search.search_manager');
        return $searchManager;
    }

    public function generateStructureIndex($count)
    {
        for ($i = 1; $i <= $count; $i++) {
            $structure = new DefaultStructureCache();
            $structure->setUuid($i);
            $structure->getProperty('title')->setValue('Structure Title ' . $i);

            $structure->getProperty('url')->setValue('/');
            $structure->setNodeState(StructureInterface::STATE_PUBLISHED);
            $structure->setLanguageCode('de');

            $this->getSearchManager()->index($structure);
        }
    }

    public function indexStructure($title, $url)
    {
        $data = array(
            'title' => $title,
            'url' => $url
        );

        $session = $this->getContainer()->get('doctrine_phpcr')->getConnection();

        try {
            $node = $session->getNode('/cmf/sulu_io/routes/de'.$url);
            $node->remove();
            $session->save();
        } catch (\PHPCR\PathNotFoundException $e) {
        }

        /** @var ContentMapperInterface $mapper */
        $mapper = $this->getContainer()->get('sulu.content.mapper');
        $mapper->save($data, 'default', 'sulu_io', 'de', 1, true, null, null, Structure::STATE_PUBLISHED);
    }
}

