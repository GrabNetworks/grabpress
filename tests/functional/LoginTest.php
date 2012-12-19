<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://ec2-107-22-53-4.compute-1.amazonaws.com/wordpress/");
  }

  public function testMyTestCase()
  {
    $this->open("/wordpress/wp-login.php?redirect_to=http%3A%2F%2Fec2-107-22-53-4.compute-1.amazonaws.com%2Fwordpress%2Fwp-admin%2F&reauth=1");
    $this->type("id=user_login", "user");
    $this->type("id=user_pass", "bitnami");
    $this->click("id=wp-submit");
    $this->waitForPageToLoad("30000");
    $this->click("link=GrabPress");
    $this->waitForPageToLoad("30000");
    $this->type("id=name", "Ejemplo1");
    $this->click("//button[@type='button']");
    $this->click("//div[7]/div/ul/li[2]/a/span[2]");
    $this->click("id=ui-multiselect-channel-select-option-3");
    $this->click("id=ui-multiselect-channel-select-option-2");
    $this->click("id=ui-multiselect-channel-select-option-1");
    $this->click("id=ui-multiselect-channel-select-option-0");
    $this->click("//button[@type='button']");
    $this->click("id=ui-multiselect-channel-select-option-28");
    $this->type("id=keyword-input", "justin");
    $this->click("id=btn-create-feed");
  }
}
?>