Feature:
  Scenario:
    Given I want to check the availability of "totallyrandomdomainname.com"
    And it turns out to be available
    When I register it
    And I fill in my name ("Matthias Noback") and email address ("matthiasnoback@gmail.com")
    And I pay EUR 10.00 for it
    Then the domain name is mine
