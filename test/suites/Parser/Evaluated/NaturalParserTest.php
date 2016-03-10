<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\ResultSet;

/**
 * Testing parser is hard, because it relies on google pages
 *
 * The tests here are parsing a saved html version of a google page.
 * They do not prevent google from changing its dom. If it
 * happens the saved html and the following tests must be updated.
 *
 * When the tests are updated, make sure that the new one include the same kind of results.
 * For instance if the previous test included a ``inDepthArticle`` the new test should do so.
 *
 *
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\ClassicalResult
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\SearchResultGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\TweetsCarousel
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\InTheNews
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Divider
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\ImageGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Video
 */
class NaturalParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParser1()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getType();
        }

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            'classical',
            'tweetsCarousel',
            'classical',
            'inTheNews',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical'
        ], $types);


        $inTheNews = $result->getResultsByType('inTheNews');
        $this->assertEquals(3, $inTheNews[0]->getRealPosition());
        $this->assertEquals(
            'How well do you know The Simpsons? Take our quiz',
            $inTheNews[0]->getDataValue('cards')[0]['title']
        );
    }

    public function testParser2()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.com.au/search?q=simpsons+donut');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+donut.html'), $gUrl, $gUrl);

        $naturalParser = new  \Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getType();
        }

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(8, $result);
        $this->assertEquals([
            'imageGroup',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical'
        ], $types);
    }

    public function testParser3()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+movie+trailer.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getType();
        }

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            'video',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical',
            'classical'
        ], $types);
    }
}
