<?php

namespace RenderAPI;

use RenderAPI\AbstractRenderable;

Class RenderAPITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_RenderableBuilder()
    {
        $builder = RenderAPI::create('ThemeNode', array(
            'node' => node_load(123),
        ));

        $this->assertInstanceOf('RenderAPI\RenderableBuilder', $builder);
    }

    /**
     * @test
     */
    public function it_creates_an_array_of_nodes() {
        $builder = RenderAPI::create(array(
            'nodes' => RenderAPI::create(array(
                RenderAPI::create('RenderAPI\RenderableBuilderCollection', array(
                    'node' => node_load(456),
                )),
                RenderAPI::create('RenderAPI\RenderableBuilderCollection', array(
                    'node' => node_load(789),
                )),
            )),
        ));

        $this->assertInstanceOf('RenderAPI\RenderableBuilderCollection', $builder);
    }


    /**
     * @test
     */
    public function test_creation_of_some_examples() {

      $items = array('item-1','item-2','item-3');

      $builder = RenderAPI::create('ThemeSomeExamples', array(
        'examples' => RenderAPI::create('ThemeItemList', array(
          'items' => $items,
        )),
      ));

      
      $this->assertTrue($builder);

    }
}

function node_load($nid) {
    switch ($nid) {
        case 123:
            $body = '<p>Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Maecenas sed diam eget risus varius blandit sit amet non magna.</p>';
            break;
        case 456:
            $body = '<p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean lacinia bibendum nulla sed consectetur.</p>';
            break;
        default:
            $body = '<p>Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Etiam porta sem malesuada magna mollis euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam quis risus eget urna mollis ornare vel eu leo.</p>';
            break;
    }
}

class ThemeNode extends AbstractRenderable {

  protected $templateName = 'node';

  function prepare() {

    // Setup variables available in template.
    $this->set('title', $this->get('node')->title);

    $this->set('content', $this->get('node')->body);

  }
}
