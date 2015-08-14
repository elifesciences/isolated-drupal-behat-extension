Feature: Isolated Drupal 7 runs
    In order to have consistent and safe test runs
    As a Drupal developer
    I need each scenario to run in isolation

    Scenario: Scenarios run in isolation from the main site
        Given Drupal is installed
        And the "site_name" variable is set to "My site"
        And a file named "behat.yml" with:
            """
            default:
                suites:
                    default:
                        contexts:
                          - Behat\MinkExtension\Context\MinkContext: ~
                          - Drupal\DrupalExtension\Context\DrupalContext: ~
                extensions:
                    Behat\MinkExtension:
                        goutte: ~
                        base_url: '{{ base_url }}'
                    Drupal\DrupalExtension:
                        blackbox: ~
                        api_driver: 'drupal'
                        drush:
                            binary: '{{ drush }}'
                            root: '{{ drupal_root }}'
                        drupal:
                            drupal_root: '{{ drupal_root }}'
                    eLife\IsolatedDrupalBehatExtension:
                        db_url: '{{ db_url }}'
            """
        And a file named "features/client.feature" with:
            """
            Feature: Setting the site name
                In order to understand what site I am on
                As a website user
                I need to be able to see the site name

            Scenario: View name
                When I go to the homepage
                Then I should see "Site-Install" in the "#name-and-slogan" element
            """
        When I run "behat --format progress features/client.feature"
        Then it should pass with:
            """
            ..

            1 scenario (1 passed)
            2 steps (2 passed)
            """

    Scenario: Scenarios run in isolation from each other
        Given a file named "behat.yml" with:
            """
            default:
                suites:
                    default:
                        contexts:
                          - FeatureContext: ~
                          - Behat\MinkExtension\Context\MinkContext: ~
                          - Drupal\DrupalExtension\Context\DrupalContext: ~
                extensions:
                    Behat\MinkExtension:
                        goutte: ~
                        base_url: '{{ base_url }}'
                    Drupal\DrupalExtension:
                        blackbox: ~
                        api_driver: 'drupal'
                        drush:
                            binary: '{{ drush }}'
                            root: '{{ drupal_root }}'
                        drupal:
                            drupal_root: '{{ drupal_root }}'
                    eLife\IsolatedDrupalBehatExtension:
                        db_url: '{{ db_url }}'
            """
        And a file named "features/bootstrap/FeatureContext.php" with:
            """
            <?php

            use Behat\Behat\Context\Context;

            class FeatureContext implements Context
            {
                /**
                 * @Given the :arg1 variable is set to :arg2
                 */
                public function theVariableIsSetTo($variable, $value)
                {
                    variable_set($variable, $value);
                }
            }
            """
        And a file named "features/client.feature" with:
            """
            Feature: Setting the site name
                In order to understand what site I am on
                As a website user
                I need to be able to see the site name

            Scenario: Default name is Set
                When I go to the homepage
                Then I should see "Site-Install" in the "#name-and-slogan" element

            Scenario: New name is Set
                Given the "site_name" variable is set to "My site"
                When I go to the homepage
                Then I should see "My site" in the "#name-and-slogan" element

            Scenario: Default name is still set
                When I go to the homepage
                Then I should see "Site-Install" in the "#name-and-slogan" element
            """
        When I run "behat --format progress features/client.feature"
        Then it should pass with:
            """
            .......

            3 scenarios (3 passed)
            7 steps (7 passed)
            """
