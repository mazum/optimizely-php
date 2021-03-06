<?php
namespace OptimizelyPHPTest\Resource\v2;

use PHPUnit_Framework_TestCase;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Experiment;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Change;
use WebMarketingROI\OptimizelyPHP\Resource\v2\ChangeAttribute;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Metric;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Schedule;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Variation;
use WebMarketingROI\OptimizelyPHP\Resource\v2\Action;

class ExperimentTest extends PHPUnit_Framework_TestCase
{
    public function testCreateNewExperiment()
    {
        $experiment = new Experiment();
        $experiment->setProjectId(152345);
        $experiment->setCampaignId(2000);
        $experiment->setAudienceIds(array(1234, 1212, 1432));        
        $experiment->setName('Test Experiment');        
        $experiment->setDescription('Some experiment');
        $curDate = date('Y-m-d H:i:s');
        $experiment->setCreated($curDate);
        $experiment->setLastModified($curDate);
        $experiment->setHoldback(5000);
        $experiment->setKey('home_page_experiment');
        $experiment->setStatus('active');
        $experiment->setIsClassic(true);
        $experiment->setId(9854);
        $experiment->setType('a/b');
        $experiment->setAudienceConditions("[\"and\", {\"audience_id\": 7000}, {\"audience_id\":7001}]");
        
        $change = new Change();
        $change->setType('custom_code');
        $change->setAllowAdditionalRedirect(true);
        $change->setAsync(true);
        $change->setSelector("a[href*=\"optimizely\"]");
        $change->setDependencies(array(24, 26));
        $change->setDestination('https://app.optimizely.com/');
        $change->setPreserveParameters(true);
        $change->setSrc(524);
        $change->setValue('window.someGlobalFunction();');
        $change->setId('string');
        $change->setSelector("a[href*=\"optimizely\"]");
        $change->setRearrange("{\"insertSelector\": \".greyBox\", \"operator\": \"before\"}");
        $change->setAttributes(new ChangeAttribute(array(
            "class" => "intro",
            "hide" => true,
            "href" => "example.com",
            "html" => "New Title",
            "remove" => true,
            "src" => "song.mp3",
            "style" => "background-color:blue;",
            "text" => "Some nice message"
        )));
        $experiment->setChanges(array($change));
        
        
        $metric = new Metric();
        $metric->setEventId(0);
        $metric->setField('revenue');
        $metric->setAggregator('unique');
        $metric->setScope('session');
        $experiment->setMetrics(array($metric));
        
        $schedule = new Schedule();
        $schedule->setStartTime('2016-10-14T03:16:51.754Z');
        $schedule->setStopTime('2016-10-14T03:16:51.754Z');
        $schedule->getTimezone('UTC');
        $experiment->setSchedule($schedule);
        
        $variation = new Variation();
        $variation->setArchived(true);
        $variation->setKey('blue_button_variation');
        $variation->setName('Blue Button');
        $variation->setVariationId(0);
        $variation->setWeight(0);
        
        $action = new Action();
        $action->setChanges(array($change));
        $action->setPageId(0);
        $variation->setActions(array($action));
        
        $experiment->setVariations(array($variation));
        
        $this->assertEquals('152345', $experiment->getProjectId());
        $this->assertEquals('2000', $experiment->getCampaignId());
        $this->assertEquals(array(1234, 1212, 1432), $experiment->getAudienceIds());
        $this->assertEquals('Test Experiment', $experiment->getName());
        $this->assertEquals('Some experiment', $experiment->getDescription());
        $this->assertEquals($curDate, $experiment->getCreated());
        $this->assertEquals($curDate, $experiment->getLastModified());
        $this->assertEquals(5000, $experiment->getHoldback());
        $this->assertEquals('home_page_experiment', $experiment->getKey());
        $this->assertEquals('active', $experiment->getStatus());
        $this->assertEquals(true, $experiment->getIsClassic());
        $this->assertEquals(9854, $experiment->getId());
        $changes = $experiment->getChanges();
        $this->assertEquals('custom_code', $changes[0]->getType());
        $this->assertEquals('intro', $changes[0]->getAttributes()->getClass());
        $this->assertEquals(true, $changes[0]->getAttributes()->getHide());
        $metrics = $experiment->getMetrics();
        $this->assertEquals('unique', $metrics[0]->getAggregator());
        $this->assertEquals('2016-10-14T03:16:51.754Z', $experiment->getSchedule()->getStartTime());
        $variations = $experiment->getVariations();
        $this->assertEquals('blue_button_variation', $variations[0]->getKey());
        
    }
    
    public function testCreateNewExperimentWithOptions()
    {
        $options = array(
            "project_id" => 1000,
            "audience_ids" => array(
              1234,
              1212,
              1432
            ),
            "campaign_id" => 2000,
            "audience_conditions" => "[\"and\", {\"audience_id\": 7000}, {\"audience_id\":7001}]",
            "changes" => array(
              array(
                "type" => "custom_code",
                "allow_additional_redirect" => true,
                "async" => true,
                "attributes" => array(
                    "class" => "intro",
                    "hide" => true,
                    "href" => "example.com",
                    "html" => "New Title",
                    "remove" => true,
                    "src" => "song.mp3",
                    "style" => "background-color:blue;",
                    "text" => "Some nice message"
                ),                
                "dependencies" => array(
                  24,
                  26
                ),
                "destination" => "https://app.optimizely.com/",
                "extension_id" => 1234,
                "preserve_parameters" => true,
                "rearrange" => "{\"insertSelector\": \".greyBox\", \"operator\": \"before\"}",
                "selector" => "a[href*=\"optimizely\"]",
                "src" => 524,
                "value" => "window.someGlobalFunction();"
              )
            ),
            "type" => "a/b",
            "description" => "string",
            "holdback" => 5000,
            "key" => "home_page_experiment",
            "metrics" => array(
              array(
                "aggregator" => "unique",
                "event_id" => 0,
                "field" => "revenue",
                "scope" => "session"
              )
            ),
            "name" => "Blue Button Experiment",
            "schedule" => array(
              "start_time" => "2016-10-14T05:08:42.850Z",
              "stop_time" => "2016-10-14T05:08:42.850Z",
              "time_zone" => "UTC"
            ),
            "status" => "active",
            "variations" => array(
              array(
                "actions" => array(
                  array(
                    "changes" => array(
                      array(
                        "type" => "custom_code",
                        "allow_additional_redirect" => true,
                        "async" => true,
                        "selector" => "a[href*=\"optimizely\"]",
                        "dependencies" => array(
                          24,
                          26
                        ),
                        "destination" => "https://app.optimizely.com/",
                        "extension_id" => 1234,
                        "preserve_parameters" => true,
                        "src" => 524,
                        "value" => "window.someGlobalFunction();"
                     )
                    ),
                    "page_id" => 0
                  )
                ),
                "archived" => true,
                "key" => "blue_button_variation",
                "name" => "Blue Button",
                "variation_id" => 0,
                "weight" => 0,                
              )
            )
        );
        
        $experiment = new Experiment($options);        
        
        $this->assertEquals('1000', $experiment->getProjectId());
        $this->assertEquals('Blue Button Experiment', $experiment->getName());        
    }
    
    public function testToArray()
    {
        $options = array(
            "project_id" => 1000,
            "audience_ids" => array(
              1234,
              1212,
              1432
            ),
            "campaign_id" => 2000,
            "changes" => array(
              array(
                "type" => "custom_code",
                "allow_additional_redirect" => true,
                "async" => true,
                "attributes" => array(
                    "class" => "intro",
                    "hide" => true,
                    "href" => "example.com",
                    "html" => "New Title",
                    "remove" => true,
                    "src" => "song.mp3",
                    "style" => "background-color:blue;",
                    "text" => "Some nice message"
                ), 
                "selector" => "a[href*=\"optimizely\"]",
                "dependencies" => array(
                  24,
                  26
                ),
                "destination" => "https://app.optimizely.com/",
                "extension_id" => 1234,
                "preserve_parameters" => true,
                "src" => 524,
                "value" => "window.someGlobalFunction();"
              )
            ),
            "description" => "string",
            "holdback" => 5000,
            "key" => "home_page_experiment",
            "metrics" => array(
              array(
                "aggregator" => "unique",
                "event_id" => 0,
                "field" => "revenue",
                "scope" => "session"
              )
            ),
            "name" => "Blue Button Experiment",
            "schedule" => array(
              "start_time" => "2016-10-14T05:08:42.850Z",
              "stop_time" => "2016-10-14T05:08:42.850Z",
              "time_zone" => "UTC"
            ),
            "status" => "active",
            "variations" => array(
              array(
                "actions" => array(
                  array(
                    "changes" => array(
                      array(
                        "type" => "custom_code",
                        "allow_additional_redirect" => true,
                        "async" => true,
                        "selector" => "a[href*=\"optimizely\"]",
                        "dependencies" => array(
                          24,
                          26
                        ),
                        "destination" => "https://app.optimizely.com/",
                        "extension_id" => 1234,
                        "preserve_parameters" => true,
                        "src" => 524,
                        "value" => "window.someGlobalFunction();"
                     )
                    ),
                    "page_id" => 0
                  )
                ),
                "archived" => true,
                "key" => "blue_button_variation",
                "name" => "Blue Button",
                "variation_id" => 0,
                "weight" => 0
              )
            )
        );
        
        $experiment = new Experiment($options);     
        
        $this->assertEquals($options, $experiment->toArray());        
    }
}
