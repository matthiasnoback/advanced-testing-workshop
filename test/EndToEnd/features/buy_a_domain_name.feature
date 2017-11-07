Feature:
  Scenario: Buying an available domain name
    Given I am on the homepage
    When I fill in "Domain name" with "totallyrandomdomainname.com"
    And I press "Check availability"
    Then I should see "totallyrandomdomainname.com is still available"
    When I press "Register it now"
    Then I should see "Fill in your details"
    When I fill in the following:
      | Name          | Matthias                  |
      | Email address | matthiasnoback@gmail.com  |
    And I press "Buy this domain name"
    Then I should see "Pay for totallyrandomdomainname.com"
    And I should see "Amount: €10,00"
    When I press "Pay"
    Then I should see "You are now the owner of totallyrandomdomainname.com"

  Scenario: Trying to buy an unavailable domain name
    Given I am on the homepage
    When I fill in "Domain name" with "google.com"
    And I press "Check availability"
    Then I should see "google.com is not available anymore"

  Scenario: Filling in invalid details
    Given I am on the homepage
    When I fill in "Domain name" with "totallyrandomdomainname.com"
    And I press "Check availability"
    Then I should see "totallyrandomdomainname.com is still available"
    When I press "Register it now"
    Then I should see "Fill in your details"
    When I fill in the following:
      | Name          |                      |
      | Email address | invalid-emailaddress |
    And I press "Buy this domain name"
    Then I should see "Please fill in your name"
    And I should see "Please fill in a valid email address"
