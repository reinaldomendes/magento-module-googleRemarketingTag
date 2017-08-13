<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Rbm_GoogleRemarketing_Helper_DataSpec extends ObjectBehavior
{
    // function let(\Mage $object)
    // {
    //     #$object->shouldHaveType('Mage');
    // //    $this->beConstructedWith($object);
    // }
    function it_is_initializable()
    {
        $this->shouldHaveType('Rbm_GoogleRemarketing_Helper_Data');
    }

    function it_enabled_should_return_bool(){
      $this->isEnabled()->shouldBeBool();
    }

    function it_getEcommPageType_should_be_homepage(){
      $this->isHomePage()->willReturn(true);
      $this->getEcommPageType()->shouldBe('homepage');
    }
}
