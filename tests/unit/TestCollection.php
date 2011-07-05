<?php

require_once 'PHPUnit/Framework/TestCase.php';

		require_once(dirname(dirname(dirname(__FILE__))).'/framework/system/System.class.php');
		$system = System::getInstance();
		$system->init();
		
/**
 * Collection test case.
 */
class TestCollection extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Collection
	 */
	private $Collection;
	
	private $callbackResponse = false;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->Collection = new Collection(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated TestCollection::tearDown()
		

		$this->Collection = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		
	}
	
	/**
	 * Tests Collection->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated TestCollection->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		//$this->Collection->__construct(/* parameters */);
	
	}
	
	public function testOnAdd()
	{
		$c = new Collection();
		$c->OnAdd = array($this, 'evtCallback');
		
		$c->add(123);
		
		$this->assertTrue($this->callbackResponse, 'Callback function for event onAdd wasn\'t called');
	}
	
	public function evtCallback()
	{
		$this->callbackResponse = true;
	}
	
	/**
	 * Tests Collection->getReadOnly()
	 */
	public function testGetReadOnly() {
		// TODO Auto-generated TestCollection->testGetReadOnly()
		$this->markTestIncomplete ( "getReadOnly test not implemented" );
		
		$this->Collection->getReadOnly(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->add()
	 */
	public function testAdd() {
		// TODO Auto-generated TestCollection->testAdd()
		$this->markTestIncomplete ( "add test not implemented" );
		
		$this->Collection->add(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->addAll()
	 */
	public function testAddAll() {
		// TODO Auto-generated TestCollection->testAddAll()
		$this->markTestIncomplete ( "addAll test not implemented" );
		
		$this->Collection->addAll(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->clear()
	 */
	public function testClear() {
		// TODO Auto-generated TestCollection->testClear()
		$this->markTestIncomplete ( "clear test not implemented" );
		
		$this->Collection->clear(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->contains()
	 */
	public function testContains() {
		// TODO Auto-generated TestCollection->testContains()
		$this->markTestIncomplete ( "contains test not implemented" );
		
		$this->Collection->contains(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->containsAll()
	 */
	public function testContainsAll() {
		// TODO Auto-generated TestCollection->testContainsAll()
		$this->markTestIncomplete ( "containsAll test not implemented" );
		
		$this->Collection->containsAll(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->count()
	 */
	public function testCount() {
		// TODO Auto-generated TestCollection->testCount()
		$this->markTestIncomplete ( "count test not implemented" );
		
		$this->Collection->count(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->size()
	 */
	public function testSize() {
		// TODO Auto-generated TestCollection->testSize()
		$this->markTestIncomplete ( "size test not implemented" );
		
		$this->Collection->size(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->equals()
	 */
	public function testEquals() {
		// TODO Auto-generated TestCollection->testEquals()
		$this->markTestIncomplete ( "equals test not implemented" );
		
		$this->Collection->equals(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->getIterator()
	 */
	public function testGetIterator() {
		// TODO Auto-generated TestCollection->testGetIterator()
		$this->markTestIncomplete ( "getIterator test not implemented" );
		
		$this->Collection->getIterator(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->getType()
	 */
	public function testGetType() {
		// TODO Auto-generated TestCollection->testGetType()
		$this->markTestIncomplete ( "getType test not implemented" );
		
		$this->Collection->getType(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->indexOf()
	 */
	public function testIndexOf() {
		// TODO Auto-generated TestCollection->testIndexOf()
		$this->markTestIncomplete ( "indexOf test not implemented" );
		
		$this->Collection->indexOf(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->insertAt()
	 */
	public function testInsertAt() {
		// TODO Auto-generated TestCollection->testInsertAt()
		$this->markTestIncomplete ( "insertAt test not implemented" );
		
		$this->Collection->insertAt(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->isEmpty()
	 */
	public function testIsEmpty() {
		// TODO Auto-generated TestCollection->testIsEmpty()
		$this->markTestIncomplete ( "isEmpty test not implemented" );
		
		$this->Collection->isEmpty(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->item()
	 */
	public function testItem() {
		// TODO Auto-generated TestCollection->testItem()
		$this->markTestIncomplete ( "item test not implemented" );
		
		$this->Collection->item(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->remove()
	 */
	public function testRemove() {
		// TODO Auto-generated TestCollection->testRemove()
		$this->markTestIncomplete ( "remove test not implemented" );
		
		$this->Collection->remove(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->removeAll()
	 */
	public function testRemoveAll() {
		// TODO Auto-generated TestCollection->testRemoveAll()
		$this->markTestIncomplete ( "removeAll test not implemented" );
		
		$this->Collection->removeAll(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->removeAt()
	 */
	public function testRemoveAt() {
		// TODO Auto-generated TestCollection->testRemoveAt()
		$this->markTestIncomplete ( "removeAt test not implemented" );
		
		$this->Collection->removeAt(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->removeRange()
	 */
	public function testRemoveRange() {
		// TODO Auto-generated TestCollection->testRemoveRange()
		$this->markTestIncomplete ( "removeRange test not implemented" );
		
		$this->Collection->removeRange(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->replace()
	 */
	public function testReplace() {
		// TODO Auto-generated TestCollection->testReplace()
		$this->markTestIncomplete ( "replace test not implemented" );
		
		$this->Collection->replace(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->retainAll()
	 */
	public function testRetainAll() {
		// TODO Auto-generated TestCollection->testRetainAll()
		$this->markTestIncomplete ( "retainAll test not implemented" );
		
		$this->Collection->retainAll(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->toArray()
	 */
	public function testToArray() {
		// TODO Auto-generated TestCollection->testToArray()
		$this->markTestIncomplete ( "toArray test not implemented" );
		
		$this->Collection->toArray(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->setType()
	 */
	public function testSetType() {
		// TODO Auto-generated TestCollection->testSetType()
		$this->markTestIncomplete ( "setType test not implemented" );
		
		$this->Collection->setType(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->subCollection()
	 */
	public function testSubCollection() {
		// TODO Auto-generated TestCollection->testSubCollection()
		$this->markTestIncomplete ( "subCollection test not implemented" );
		
		$this->Collection->subCollection(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->offsetExists()
	 */
	public function testOffsetExists() {
		// TODO Auto-generated TestCollection->testOffsetExists()
		$this->markTestIncomplete ( "offsetExists test not implemented" );
		
		$this->Collection->offsetExists(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->offsetGet()
	 */
	public function testOffsetGet() {
		// TODO Auto-generated TestCollection->testOffsetGet()
		$this->markTestIncomplete ( "offsetGet test not implemented" );
		
		$this->Collection->offsetGet(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->offsetSet()
	 */
	public function testOffsetSet() {
		// TODO Auto-generated TestCollection->testOffsetSet()
		$this->markTestIncomplete ( "offsetSet test not implemented" );
		
		$this->Collection->offsetSet(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->offsetUnset()
	 */
	public function testOffsetUnset() {
		// TODO Auto-generated TestCollection->testOffsetUnset()
		$this->markTestIncomplete ( "offsetUnset test not implemented" );
		
		$this->Collection->offsetUnset(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->current()
	 */
	public function testCurrent() {
		// TODO Auto-generated TestCollection->testCurrent()
		$this->markTestIncomplete ( "current test not implemented" );
		
		$this->Collection->current(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->hasNext()
	 */
	public function testHasNext() {
		// TODO Auto-generated TestCollection->testHasNext()
		$this->markTestIncomplete ( "hasNext test not implemented" );
		
		$this->Collection->hasNext(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->hasPrevious()
	 */
	public function testHasPrevious() {
		// TODO Auto-generated TestCollection->testHasPrevious()
		$this->markTestIncomplete ( "hasPrevious test not implemented" );
		
		$this->Collection->hasPrevious(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->key()
	 */
	public function testKey() {
		// TODO Auto-generated TestCollection->testKey()
		$this->markTestIncomplete ( "key test not implemented" );
		
		$this->Collection->key(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->next()
	 */
	public function testNext() {
		// TODO Auto-generated TestCollection->testNext()
		$this->markTestIncomplete ( "next test not implemented" );
		
		$this->Collection->next(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->previous()
	 */
	public function testPrevious() {
		// TODO Auto-generated TestCollection->testPrevious()
		$this->markTestIncomplete ( "previous test not implemented" );
		
		$this->Collection->previous(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->rewind()
	 */
	public function testRewind() {
		// TODO Auto-generated TestCollection->testRewind()
		$this->markTestIncomplete ( "rewind test not implemented" );
		
		$this->Collection->rewind(/* parameters */);
	
	}
	
	/**
	 * Tests Collection->valid()
	 */
	public function testValid() {
		// TODO Auto-generated TestCollection->testValid()
		$this->markTestIncomplete ( "valid test not implemented" );
		
		$this->Collection->valid(/* parameters */);
	
	}

}

