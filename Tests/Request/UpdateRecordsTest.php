<?php
namespace Christiaan\ZohoCRMClient\Tests\Request;

use Christiaan\ZohoCRMClient\Request;
use Christiaan\ZohoCRMClient\Transport\MockTransport;
use Christiaan\ZohoCRMClient\Transport\TransportRequest;

class UpdateRecordsTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockTransport */
    private $transport;

    /** @var TransportRequest */
    private $request;

    /** @var Request\UpdateRecords */
    private $updateRecords;

    protected function setUp()
    {
        $this->transport = new MockTransport();
        $this->request = new TransportRequest('Leads');
        $this->request->setTransport($this->transport);
        $this->updateRecords = new Request\UpdateRecords($this->request);
    }

    public function testInitial()
    {
        $this->assertEquals('updateRecords', $this->request->getMethod());
        $this->assertEquals(
            4,
            $this->request->getParam('version')
        );
    }

    public function testId()
    {
        $this->updateRecords->id('abc123');
        $this->assertEquals(
            'abc123',
            $this->request->getParam('id')
        );
    }

    public function testAddRecord()
    {
        $this->updateRecords->addRecord(array('abc123'));

        $this->transport->response = true;

        $this->assertTrue($this->updateRecords->request());
        $this->assertEquals(array('version' => 4, 'xmlData' =>  array(array('abc123'))), $this->transport->paramList);
    }

    public function testNoIdIsSetForMultipleUpdate()
    {
        // Make sure an ID is set
        $this->testId();

        $this->updateRecords->addRecord(array('abc123'));
        $this->updateRecords->addRecord(array('abc123456'));

        $this->updateRecords->request();

        // Test that the ID is removed
        $this->assertNotEquals(
            'abc123',
            $this->request->getParam('id')
        );
    }

    public function testTriggerWorkflow()
    {
        $this->updateRecords->triggerWorkflow();
        $this->assertEquals(
            'true',
            $this->request->getParam('wfTrigger')
        );
    }

    public function testOnDuplicateUpdate()
    {
        $this->updateRecords->onDuplicateUpdate();
        $this->assertEquals(
            2,
            $this->request->getParam('duplicateCheck')
        );
    }

    public function testOnDuplicateError()
    {
        $this->updateRecords->onDuplicateError();
        $this->assertEquals(
            1,
            $this->request->getParam('duplicateCheck')
        );
    }

    public function testRequireApproval()
    {
        $this->updateRecords->requireApproval();
        $this->assertEquals(
            'true',
            $this->request->getParam('isApproval')
        );
    }
}
