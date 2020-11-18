Feature: Github CI annotations

  If we are running in a Github Action we can emit extra strings to enhance the way results are displayed

  @isolated
  Scenario: Error messages shown inline
    Given the GITHUB_ACTIONS environment variable is set to true
    And the file "spec/FailingSpec.php" contains:
       """
       <?php

       namespace spec\Cjm\PhpSpec;

       use PhpSpec\ObjectBehavior;

       class FailingSpec extends ObjectBehavior
       {
           function it_is_equal()
           {
               $this->foo()->shouldReturn(true);
           }
       }
       """
    And the file "src/Failing.php" contains:
       """
       <?php

       namespace Cjm\PhpSpec;

       class Failing
       {
           function foo() { return false; }
       }
       """
    When I run phpspec
    Then I should see:
       """
       ::error file=spec/FailingSpec.php,line=9,col=1::Failed: Expected true, but got false
       """


