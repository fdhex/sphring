<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 15/10/2014
 */


namespace Arthurh\Sphring\Extend;


use Arthurh\Sphring\AbstractTestSphring;
use Arthurh\Sphring\Sphring;

class ExtendTest extends AbstractTestSphring
{
    const SIMPLE_TEST_FILE = 'mainSimpleTest.yml';
    const ONEXIST_TEST_FILE = 'mainSimpleTestOnExist.yml';

    public function testExtendSimpleValid()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::SIMPLE_TEST_FILE);
        $sphring->getExtender()->setDefaultFilename('sphring-extend-simple.yml');
        $sphring->loadContext();
        $useBean = $sphring->getBean('usebean');
        $this->assertArrayHasKey('testExtend', $useBean->getJuju());
    }

    public function testExtendAnnotationRequired()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::ONEXIST_TEST_FILE);
        $sphring->getExtender()->setDefaultFilename('sphring-extend-onexist.yml');
        $sphring->loadContext();
        $useBean = $sphring->getBean('usebean');
        $this->assertEquals('testAnnotationExtend', $useBean->getTestingValue());
    }

    public function testExtendLoadContextSphringGlobal()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::ONEXIST_TEST_FILE);
        $sphring->getExtender()->setDefaultFilename('sphring-extend-onexist.yml');
        $sphring->loadContext();

        $this->assertEquals('test', $sphring->jojo);
    }

    public function testExtendOnExistValid()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::ONEXIST_TEST_FILE);
        $sphring->getExtender()->setDefaultFilename('sphring-extend-onexist.yml');
        $sphring->loadContext();
        $useBean = $sphring->getBean('usebean');
        $this->assertArrayHasKey('testExtend', $useBean->getJuju());
        $usefake = $sphring->getBean('usefake');
        $this->assertEquals('testBeanFake', $usefake->getTestingValue());
    }

    public function testExtendSimpleValidWithComposer()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::SIMPLE_TEST_FILE);
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $useBean = $sphring->getBean('usebean');
        $this->assertArrayHasKey('testExtend', $useBean->getJuju());
    }

    public function testExtendDefaultValidWithComposer()
    {
        $sphring = new Sphring();
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $useBean = $sphring->getBean('usebean');
        $this->assertArrayHasKey('testExtend', $useBean->getJuju());
    }

    public function testExtendNode()
    {
        $sphring = new Sphring(self::$CONTEXT_EXTEND_FOLDER . '/' . self::SIMPLE_TEST_FILE);
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $extender = $sphring->getExtender();
        $extendNodes = $extender->getExtendNodes();
        $extendNodeKey = key($extendNodes);
        $extendNode = $extendNodes[$extendNodeKey];
        $nodes = $extendNode->getNodes();


        $node = $nodes[0];
        $newNode = clone $node;
        $newNode->setEventName('test');
        $newNode->setPriority(2);
        $extendNode->addNode($newNode);
        $extendNode->removeNode($node);

        $this->assertNotEmpty($extendNode->getNodes()[0]);
        $this->assertTrue($node !== $extendNode->getNodes()[0]);
        $extendNode->setNodes($nodes);
        $this->assertNotEmpty($extendNode->getNodes()[0]);
        $this->assertTrue($node === $extendNode->getNodes()[0]);
    }

    public function testExtendMethodCallAfterComposer()
    {
        $sphring = new Sphring();
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $foo = $sphring->getBean('foobean');
        $fooTest = $foo->testAfterCall('john');
        $this->assertEquals('john', $fooTest);
    }

    public function testExtendMethodCallBeforeComposer()
    {
        $sphring = new Sphring();
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $foo = $sphring->getBean('foobean');
        $fooTest = $foo->testBeforeCall('johny');
        $this->assertEquals('johny', $fooTest);
    }

    public function testExtendClassInstantiateComposer()
    {
        $sphring = new Sphring();
        $sphring->setRootProject(__DIR__ . '/../Resources/composer');
        $sphring->loadContext();
        $foo = $sphring->getBean('foobean');
        $this->assertTrue($foo->getTestingValue());
    }
} 