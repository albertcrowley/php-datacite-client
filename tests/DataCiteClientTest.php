<?php

require_once('../common/include/DataCiteClient.php');


class DataCiteClientTest extends PHPUnit_Framework_TestCase
{
	/** @var DataCiteClient  $client */
    private $client;
    public static $test_url_target = 'https://www.nitrc.org/';
    public static $test_doi = "10.5072/nitrc/test2";
    public static $sample_xml_filename = "./tests/DOI/sample.xml";

    /** @test */
//    public function test_construct_object()
//    {
//        $this->assertEquals(
//            $this->client->getDataciteUrl(),getenv('DATACITE_URL')
//        );
//    }

    /** @test */
    public function it_should_return_a_doi()
    {
        $get = $this->client->get(DataCiteClientTest::$test_doi);
        $this->assertEquals($get, DataCiteClientTest::$test_url_target);
        $this->assertFalse($this->client->hasError());
    }

    /** @test */
    public function it_should_return_good_xml_for_a_doi()
    {
        $actual = new DOMDocument;
        $metadata = $this->client->getMetadata( DataCiteClientTest::$test_doi);
        $actual->loadXML($metadata);

        $this->assertFalse($this->client->hasError());
        $this->assertEquals("resource", $actual->firstChild->tagName);
    }

    /** @test */
    public function test_mint_a_new_doi()
    {
        $xml = file_get_contents(DataCiteClientTest::$sample_xml_filename);
        $response = $this->client->mint(DataCiteClientTest::$test_doi, DataCiteClientTest::$test_url_target, $xml);
        $this->assertTrue($response);

        // now test that we can update landing pages
	    $new_url = "https://www.nitrc.org/updatelandingpage";
	    $old_url = $this->client->get(DataCiteClientTest::$test_doi);
	    $this->assertEquals(DataCiteClientTest::$test_url_target, $old_url, "The DOI didn't have the initial landing page that was expected.");
	    $this->client->updateLandingPageURL(DataCiteClientTest::$test_doi, $new_url);
	    $this->assertEquals($new_url, $this->client->get(DataCiteClientTest::$test_doi), "Landing page URL wasn't updated as expected.");

    }

    /** @test */
    public function it_should_set_datacite_url()
    {
        //setdatacite_url
        //make sure it's the new one
    }

    /** @test */
    public function it_should_update_a_doi_with_new_xml()
    {
        //run update with new xml
        //get the new xml and make sure it's the same
        //put old xml back?
    }

    /** @test */
    public function it_should_activate_a_doi_and_then_deactivate()
    {
        //make sure the DOI is activated
        //deactivate it
        //make sure it's deactivated
        //activate it
        //make sure it's activated in the status
    }

    /**
     * Run Once before all other tests
     * setup the client
     */
    protected function setUp()
    {
	    global $sys_datacite_username, $sys_datacite_password, $sys_datacite_url ;

	    $this->client = new DataCiteClient($sys_datacite_username, $sys_datacite_password);
        $this->client->setDataciteUrl($sys_datacite_url);
    }

    public static function setupBeforeClass() {
	    global $sys_datacite_username, $sys_datacite_password, $sys_datacite_url ;
	    $xml = file_get_contents(DataCiteClientTest::$sample_xml_filename);
	    $client = new DataCiteClient($sys_datacite_username, $sys_datacite_password);
	    $client->setDataciteUrl($sys_datacite_url);
	    $response = $client->mint(DataCiteClientTest::$test_doi, DataCiteClientTest::$test_url_target, $xml);
	    if ( ! $response) {
	    	throw new Exception("Error during DataCiteClientTest setup");
	    }
    }

}